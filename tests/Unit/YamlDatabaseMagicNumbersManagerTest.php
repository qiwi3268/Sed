<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Lib\DatabaseMagicNumbers\Exceptions\ContainerDoesNotExistException;
use App\Lib\DatabaseMagicNumbers\Exceptions\KeyInContainerDoesNotExistException;

use PHPUnit\Framework\TestCase;
use App\Lib\Settings\SettingsParser;
use App\Services\Settings\YamlDatabaseMagicNumbersManager;


class YamlDatabaseMagicNumbersManagerTest extends TestCase
{
    public YamlDatabaseMagicNumbersManager $manager;


    public function setUp(): void
    {
        $this->manager = new YamlDatabaseMagicNumbersManager(new FakeYamlDatabaseMagicNumbersManagerSettingsParser());
    }


    public function testContainerDoesNotExistException(): void
    {
        $this->expectException(ContainerDoesNotExistException::class);
        $this->expectExceptionMessage("Контейнер: 'cont3' не существует");
        $this->manager->getContainer('cont3');
    }


    public function testKeyInContainerDoesNotExistException(): void
    {
        $this->expectException(KeyInContainerDoesNotExistException::class);
        $this->expectExceptionMessage("Ключ: 'kkk' отсутствует в контейнере: 'cont1'");
        $this->manager->getContainer('cont1')->get('kkk');
    }


    public function testOk(): void
    {
        $c = $this->manager->getContainer('cont1');

        $this->assertSame('val1', $c->get('key1'));
        $this->assertSame('v2', $c->get('key_2'));
        $this->assertSame(3, $c->get('k3'));
    }
}



class FakeYamlDatabaseMagicNumbersManagerSettingsParser implements SettingsParser
{
    public function getSchema(): array
    {
        return [
            'cont1'  => [
                'key1'  => 'val1',
                'key_2' => 'v2',
                'k3'    => 3
            ],
            'cont2' => [
                'k1' => 1
            ],
        ];
    }
}
