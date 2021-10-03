<?php

declare(strict_types=1);

namespace App\Services\Files\FileToPngConverting;

use Throwable;
use App\Services\Files\FileToPngConverting\Exceptions\FileFormatDetectorException;

use wapmorgan\FileTypeDetector\Detector;


final class FileFormatDetector
{
    public const FORMATS = [
        'doc'  => 'doc',
        'docx' => 'docx',
        'pdf'  => 'pdf'
    ];


    /**
     * @throws FileFormatDetectorException
     */
    public static function detect(string $path): string
    {
        try {
            $type = Detector::detectByContent($path);
        } catch (Throwable $e) {
            throw new FileFormatDetectorException("Исключение при работе Detector::detectByContent для файла: '$path'", 0, $e);
        }

        if ($type === false) {
            throw new FileFormatDetectorException("Detector::detectByContent вернул false для файла: '$path'");
        }

        $format = $type[1];

        if (!in_array($format, self::FORMATS)) {
            FileFormatDetectorException::unknownFormat($format, $path);
        }
        return $format;
    }
}
