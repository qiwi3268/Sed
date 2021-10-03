<?php

declare(strict_types=1);

namespace App\Lib\Csp\Certification;

use App\Lib\Csp\Exceptions\ParsingException;


final class MessageParser
{

    public function __construct(private string $message)
    {}


    /**
     * Возвращает массив личных сертификатов
     *
     * @return PersonalCertificate[]
     */
    public function parse(): array
    {
        // Разделение выходного сообщения на сертификаты.
        // Сертификаты разделены строкой вида '1-------'
        $rawCertificates = preg_split('/\d+-{5,}/', $this->message, -1, PREG_SPLIT_NO_EMPTY);

        $result = [];

        foreach ($rawCertificates as $rawCertificate) {

            // Фильтрация сырых сертификатов
            if (str_icontains_all($rawCertificate, ['Issuer', 'Subject', 'Serial', 'Not valid before', 'Not valid after'])) {

                $tokenized = $this->tokenize($rawCertificate);
                $issuer = $this->explodeCertificateFields($tokenized['issuer']);
                $subject = $this->explodeCertificateFields($tokenized['subject']);

                if (PersonalCertificate::isPersonal($issuer, $subject)) {

                    $result[] = new PersonalCertificate(
                        $issuer,
                        $subject,
                        $tokenized['serial'],
                        $this->convertDate($tokenized['not_valid_before']),
                        $this->convertDate($tokenized['not_valid_after'])
                    );
                }
            }
        }
        return $result;
    }


    /**
     * Разбивает строку сертификата в ассоциативный массив,
     * фильтрует ненужные поля
     *
     * @throws ParsingException
     */
    private function tokenize(string $certificate): array
    {
        $result = [];

        foreach (explode(PHP_EOL, $certificate) as $part) {

            if (str_icontains_any($part, ['Issuer', 'Subject', 'Serial', 'Not valid before', 'Not valid after'])) {

                $divided = preg_split('/\s+:\s+/', $part);

                if (count($divided) != 2) {
                    throw new ParsingException("Ошибка при трансформации строки сертификата: '$part' в массив");
                }

                // Приведение к нижнему регистру
                // Замена всех пробелов, чтобы хранить первую часть в виде ключа массива
                $key = mb_strtolower(str_replace(' ', '_', trim($divided[0])));

                $result[$key] = trim($divided[1]);
            }
        }
        if (count($result) != 5) {
            throw new ParsingException('Массив сертификата имеет некорректную структуру: ' . implode(', ', array_keys($result)));
        }
        return $result;
    }


    /**
     * Разбивает поля издателя и владельца сертификата в ассоциативный массив
     */
    private function explodeCertificateFields(string $certificateFields): array
    {
        // Искусственное добавление ', ' к первому полю
        $fields = preg_split('/,\s([a-z]+)=/i', ", $certificateFields", -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        $result = [];

        $iterations = count($fields) - 1;

        // Инкремент по 2, так как цикл идёт по названиям полей, пропуская значения
        for ($i = 0; $i < $iterations; $i += 2) {
            // По имени поля записывается его значение
            $result[$fields[$i]] = $fields[$i + 1];
        }
        return $result;
    }


    /**
     * Конвертирует строку с датой и временем к формату d.m.Y H:i:s
     *
     * @return string формат d.m.Y H:i:s
     * @throws ParsingException
     */
    private function convertDate(string $date): string
    {
        if (!pm('/(\d{2}\/\d{2}\/\d{4})\s+(\d{2}:\d{2}:\d{2})/', $date, $datetime)) {
            throw new ParsingException("Ошибка при разборе строки даты: '$date'");
        }
        [$date, $time] = $datetime;

        return  str_replace('/', '.', $date) . " $time";
    }
}
