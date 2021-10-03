<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Throwable;
use Illuminate\Console\Command;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\Table;
use PhpOffice\PhpWord\Element\Cell;
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;


final class TestCommand extends Command
{

    protected $signature = 'test_command';

    protected $description = 'Тестовая команда';


    public function handle(): int
    {
        $dir = '/var/www/html/storage/app/tmp/conclusions';

        $names = array_filter(scandir($dir), function (string $name): bool {
            $name = mb_strtolower($name);
            return str_starts_with($name, 'заключение') && str_contains($name, '.doc');
        });

        foreach ($names as $name) {

            $path = "$dir/$name";

            $reader = IOFactory::createReader();

            try {
                /** @var PhpWord $word */
                $word = $reader->load($path);
            } catch (Throwable $e) {
                $this->error("Ошибка файла $name");
                continue;
            }

            // Получение последней таблицы
            foreach (array_reverse($word->getSections()[0]->getElements()) as $element) {

                if ($element instanceof Table) {
                    $lastTable = $element;
                    break;
                }
            }

            foreach ($lastTable->getRows() as $row) {

                $cells = $row->getCells();

                if (count($cells) != 3) {
                    continue;
                }

                // Фио
                $fio = $this->getCellText($cells[1]);

                if (str_icontains($fio, 'громов')) {

                    // Направление
                    $direction = $this->getCellText($cells[0]);

                    if (str_icontains_all($direction, ['конструктивные', 'объемно', 'планировочные ', 'решения'])) {
                        $this->info($name);
                    }
                }
            }
        }
        $this->info('Команда выполнена успешно');
        return 0;
    }


    private function getCellText(Cell $cell): string
    {
        $result = '';

        foreach ($cell->getElements() as $element) {

            if ($element instanceof TextRun) {

                /** @var Text $text */
                foreach ($element->getElements() as $text) {
                    $result .= $text->getText();
                }
            }
        }
        return $result;
    }
}
