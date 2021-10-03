<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Lib\Singles\HashArray;


class HashArrayTest extends TestCase
{

    public function testOk(): void
    {
        $hashArray = new HashArray();

        $hashArray->add($el1 = 'el1');
        $hashArray->add($el2 = 'el2');
        $hashArray->add($el3 = 'el3');

        $this->assertTrue($hashArray->has($el1));
        $this->assertTrue($hashArray->has($el2));
        $this->assertTrue($hashArray->has($el3));

        $this->assertFalse($hashArray->has('el'));
        $this->assertFalse($hashArray->has('el4'));
        $this->assertFalse($hashArray->has('El1'));

        $this->assertEquals([$el1, $el2, $el3], $hashArray->getElements());
    }


    public function testAddException1(): void
    {
        $hashArray = new HashArray();
        $this->expectExceptionMessage('Добавляемый элемент не может быть пустой строкой');
        $hashArray->add('');
    }


    public function testAddException2(): void
    {
        $hashArray = new HashArray();
        $this->expectExceptionMessage("Добавляемый элемент: '2' не может быть числовым значением");
        $hashArray->add('2');
    }


    public function testAddException3(): void
    {
        $hashArray = new HashArray();
        $hashArray->add('foo');
        $this->expectExceptionMessage("Элемент: 'foo' уже добавлен в хэш массив");
        $hashArray->add('foo');
    }
}
