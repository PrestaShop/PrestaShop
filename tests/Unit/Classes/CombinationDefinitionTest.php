<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Classes;

use Combination;
use PHPUnit\Framework\TestCase;

class CombinationDefinitionTest extends TestCase
{
    public function testHasIsVirtualField(): void
    {
        $this->assertArrayHasKey('is_virtual', Combination::$definition['fields']);
        $this->assertFalse((new Combination())->is_virtual);
    }
}
