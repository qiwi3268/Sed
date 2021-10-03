<?php

declare(strict_types=1);


/**
 * Регистронезависимый вариант функции {@see str_contains()}
 */
function str_icontains(string $haystack , string $needle): bool
{
    return str_contains(
        mb_strtolower($haystack),
        mb_strtolower($needle)
    );
}


/**
 * Определяет, содержит ли строка все подстроки
 *
 * @param string $haystack строка, в которой производится поиск
 * @param string[] $needles подстроки
 */
function str_contains_all(string $haystack , array $needles): bool
{
    foreach ($needles as $needle) {

        if (!str_contains($haystack, $needle)) {
            return false;
        }
    }
    return true;
}


/**
 * Регистронезависимый вариант функции {@see str_contains_all()}
 */
function str_icontains_all(string $haystack , array $needles): bool
{
    return str_contains_all(
        mb_strtolower($haystack),
        array_map('mb_strtolower', $needles)
    );
}


/**
 * Определяет, содержит ли строка хоть одну подстроку
 *
 * @param string $haystack строка, в которой производится поиск
 * @param string[] $needles подстроки
 */
function str_contains_any(string $haystack , array $needles): bool
{
    foreach ($needles as $needle) {

        if (str_contains($haystack, $needle)) {
            return true;
        }
    }
    return false;
}


/**
 * Регистронезависимый вариант функции {@see str_contains_any()}
 */
function str_icontains_any(string $haystack , array $needles): bool
{
    return str_contains_any(
        mb_strtolower($haystack),
        array_map('mb_strtolower', $needles)
    );
}


/**
 * Удаляет повторяющеюся символы переноса строки
 */
function str_remove_duplicate_nl(string $string): string
{
    $parts = explode(PHP_EOL, $string);

    $result = [];

    $result[] = $parts[0];

    for ($i = 1; $i < count($parts); $i++) {

        if ($parts[$i] === '' && $parts[$i - 1] === '') {
            continue;
        }

        $result[] = $parts[$i];
    }
    return implode(PHP_EOL, $result);
}

