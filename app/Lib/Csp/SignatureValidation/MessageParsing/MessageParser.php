<?php

declare(strict_types=1);

namespace App\Lib\Csp\SignatureValidation\MessageParsing;

use App\Lib\Csp\Exceptions\ParsingException;
use App\Lib\Csp\SignatureValidation\MessageParsing\ParsingResult\ParsingResult;
use App\Lib\Csp\SignatureValidation\MessageParsing\ParsingResult\ParsingResultStructure;
use App\Lib\Csp\SignatureValidation\MessageParsing\ParsingResult\SignerPrototype;


final class MessageParser
{
    private MessageIterator $iterator;
    private Glossary $glossary;


    public function __construct(string $message)
    {
        $this->iterator = new MessageIterator($this->explodeMessage($message));
        $this->glossary = new Glossary();
    }


    /**
     * Возвращает результат разбора сообщения проверки подписи
     *
     * @throws ParsingException
     */
    public function parse(): ParsingResult
    {
        $it = $this->iterator;
        $it->rewind();

        $resultStructure = new ParsingResultStructure();

        $count = $it->count();

        // Сообщение из одного элемента - общая ошибка или errorCode
        if ($count == 1) {

            $item = $it[0];

            if ($this->glossary->errorCode($item)) {
                $resultStructure->errorCode = $item;
            } else {
                $resultStructure->commonError = $item;
            }
            return new ParsingResult($resultStructure);
        }

        // В любых других сообщениях - последний элемент errorCode
        if (!$this->glossary->errorCode($it->lastItem())) {
            throw new ParsingException('В сообщении ожидалось, что последний элемент будет errorCode');
        }

        // Сообщение из двух элементов - общая ошибка и errorCode
        if ($count == 2) {
            $resultStructure->commonError = $it[0];
            $resultStructure->errorCode = $it[1];
            return new ParsingResult($resultStructure);
        }

        /*
         * Нечётное количество элементов - успех:
         * (Signer + OK) * n + errorCode
         *
         * Чётное количество элементов - ошибка:
         * (Signer + BAD) * n + commonError + errorCode
         */

        $success = $count % 2 != 0;

        if ($success) {
            $resultStructure->errorCode = $it->lastItem();
            $resultStructure->result = true;
        } else {

            if ($this->glossary->errorDescription($it->beforeLastItem())) {
                $resultStructure->commonError = $it->beforeLastItem();
                $resultStructure->errorCode = $it->lastItem();
            } else {
                throw new ParsingException('В сообщении с ошибкой при проверке подписи ожидалось, что предпоследний элемент будет описание ошибки');
            }
        }

        $finish = $success
            ? $count - 1  // Пропуск errorCode
            : $count - 2; // Пропуск commonError и errorCode

        while ($it->key() < $finish) {

            if ($it->isEven()) {
                // Поскольку есть уверенность в том, что первым параметром передаётся информация о подписанте,
                // то префикс более не нужен
                $resultStructure->signerPrototypes[] = new SignerPrototype(
                    $this->deleteSignerInfoPrefix($it->current()),
                    $it->nextItem(),
                    $it->key()
                );
            }
            $it->next();
        }
        return new ParsingResult($resultStructure);
    }


    /**
     * Разбивает сообщение с результатом проверки подписи на составные части,
     * фильтрует технические строки
     */
    private function explodeMessage(string $message): array
    {
        $result = [];

        foreach (explode(PHP_EOL, $message) as $string) {

            $string = trim($string);

            if ($string === '') {
                continue;
            }

            // Обработка ситуаций, когда из-за отсутствия прогресс-бара проверки подписи некоторые части сообщения
            // окажутся в одной строке, т.к. символ переноса строк принадлежит прогресс-бару
            if (
                pm('/Signature verifying.*(\[ErrorCode:.*])/i', $string, $m) ||
                pm('/Signature verifying.*(Error:.*)/i', $string, $m)
            ) {
                $result[] = $m;
                continue;
            }

            if (str_icontains_any($string, [
                'Crypto-Pro',
                'Command prompt',
                'Folder',
                'Signature verifying',
                'CSPbuild'
            ])) {
                continue;
            }
            $result[] = $string;
        }
        return $result;
    }


    /**
     * Удаляет префикс из строки информации о подписанте
     *
     * @throws ParsingException
     */
    private function deleteSignerInfoPrefix(string $signerInfo): string
    {
        if (!pm('/Signer:(.+)/i', $signerInfo, $m)) {
            throw new ParsingException("В строке: '$signerInfo' отсутствует информация о подписанте");
        }
        return $m;
    }
}
