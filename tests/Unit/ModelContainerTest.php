<?php

declare(strict_types=1);

namespace Tests\Unit;

use Exception;

use PHPUnit\Framework\TestCase;
use App\Services\RequestValidation\ModelContainer;
use App\Models\User;


class ModelContainerTest extends TestCase
{
    public function testOk(): void
    {
        $modelContainer = new ModelContainer();

        $user1 = new User();
        $user2 = new User();
        $user3 = new User();
        $user4 = new User();

        $modelContainer->set($user1, 'id1');
        $modelContainer->set($user2, 'id2');
        $modelContainer->set($user3, 'id2', '');
        $modelContainer->set($user4, 'id2','some salt');

        $this->assertTrue($modelContainer->has(User::class, 'id1'));
        $this->assertTrue($modelContainer->has(User::class, 'id2'));
        $this->assertTrue($modelContainer->has(User::class, 'id2', ''));
        $this->assertTrue($modelContainer->has(User::class, 'id2', 'some salt'));

        $this->assertFalse($modelContainer->has(User::class, 'id1', ''));
        $this->assertFalse($modelContainer->has(User::class, 'id1', 'some salt'));
        $this->assertFalse($modelContainer->has(User::class, 'id2', 'some another salt'));

        $this->assertSame($user1, $modelContainer->get(User::class, 'id1'));
        $this->assertSame($user2, $modelContainer->get(User::class, 'id2'));
        $this->assertSame($user3, $modelContainer->get(User::class, 'id2', ''));
        $this->assertSame($user4, $modelContainer->get(User::class, 'id2', 'some salt'));
        $this->assertNotSame($user3, $modelContainer->get(User::class, 'id2', 'some salt'));
    }


    public function testSetDuplicateDataWithoutSaltException(): void
    {
        $modelContainer = new ModelContainer();

        $modelContainer->set(new User(), 'id');

        $this->expectException(Exception::class);

        $modelContainer->set(new User(), 'id');
    }


    public function testSetDuplicateDataWithSaltException(): void
    {
        $modelContainer = new ModelContainer();

        $modelContainer->set(new User(), 'id', 'salt');

        $this->expectException(Exception::class);

        $modelContainer->set(new User(), 'id', 'salt');
    }


    public function testGetMissingDataException(): void
    {
        $modelContainer = new ModelContainer();

        $modelContainer->set(new User(), 'id1', 'salt');

        $this->expectException(Exception::class);

        $modelContainer->get(User::class, 'id2', 'salt');
    }
}
