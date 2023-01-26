<?php

namespace Tests\Unit\Core\Security;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Security\Hashing;

class HashingTest extends TestCase
{
    public function testHash(): void
    {
        $hash = new Hashing();

        self::assertSame($hash->hash('some_data_to_hash', 'this_is_the_salt'), 'c7c8f9a991ac0dcb7a33ded423acf0d8');
    }
}
