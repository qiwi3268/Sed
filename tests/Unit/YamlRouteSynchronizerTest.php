<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Lib\RouteSynchronization\Exceptions\RouteNotFoundException;
use Tests\TestCase;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Support\Facades\URL;
use App\Lib\Settings\SettingsParser;
use App\Services\Settings\YamlRouteSynchronizer;


class YamlRouteSynchronizerTest extends TestCase
{
    public YamlRouteSynchronizer $synchronizer;


    public function setUp(): void
    {
        parent::setUp();
        $this->synchronizer = new YamlRouteSynchronizer(
            new FakeYamlRouteSynchronizerSettingsParser(), app(UrlGenerator::class)
        );
    }


    public function testRouteNotFoundException(): void
    {
        $this->expectException(RouteNotFoundException::class);
        $this->synchronizer->generateAbsoluteUrl('missing');
    }


    public function testMissingPlaceholderException(): void
    {
        $this->expectExceptionMessage("Заполнитель маршрута: 'ppp3' отсутствует");
        $this->synchronizer->generateAbsoluteUrl('test1', ['p1' => 1, 'pp2' => 2]);
    }


    public function testOk(): void
    {
        $this->assertSame(URL::to('/aaa/place1/place2/place3'), $this->synchronizer->generateAbsoluteUrl('test1', [
            'p1'   => 'place1',
            'pp2'  => 'place2',
            'ppp3' => 'place3',
        ]));
        $this->assertSame(URL::to('/place'), $this->synchronizer->generateAbsoluteUrl('test2', ['p' => 'place']));
        $this->assertSame(URL::to('/simple'), $this->synchronizer->generateAbsoluteUrl('test3'));
    }
}



class FakeYamlRouteSynchronizerSettingsParser implements SettingsParser
{
    public function getSchema(): array
    {
        return [
            'test1' => '/aaa/{p1}/{pp2}/{ppp3}',
            'test2' => '/{p}',
            'test3' => '/simple'
        ];
    }
}
