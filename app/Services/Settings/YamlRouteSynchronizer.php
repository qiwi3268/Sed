<?php

declare(strict_types=1);

namespace App\Services\Settings;

use App\Lib\RouteSynchronization\Exceptions\RouteNotFoundException;
use App\Lib\RouteSynchronization\Exceptions\MissingPlaceholderException;

use Illuminate\Contracts\Routing\UrlGenerator;
use App\Lib\Settings\SettingsParser;
use App\Lib\RouteSynchronization\RouteSynchronizer;


final class YamlRouteSynchronizer implements RouteSynchronizer
{
    private array $schema;


    public function __construct(SettingsParser $parser, private UrlGenerator $urlGenerator)
    {
        $this->schema = $parser->getSchema();
    }


    /**
     * @throws RouteNotFoundException
     * @throws MissingPlaceholderException
     */
    public function generateAbsoluteUrl(string $name, array $placeholders = []): string
    {
        if (!array_key_exists($name, $this->schema)) {
            throw new RouteNotFoundException($name);
        }

        $path = $this->schema[$name];

        preg_match_all('/{(.+)}/U', $path, $matches, PREG_SET_ORDER);

        foreach ($matches as [$toReplace, $placeholder]) {

            if (!array_key_exists($placeholder, $placeholders)) {
                throw new MissingPlaceholderException($placeholder);
            }
            $path = str_replace($toReplace, (string) $placeholders[$placeholder], $path);
        }
        return $this->urlGenerator->to($path);
    }
}
