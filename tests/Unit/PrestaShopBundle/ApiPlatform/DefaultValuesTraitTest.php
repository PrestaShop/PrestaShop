<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\ApiPlatform;

use ApiPlatform\Metadata\Operation;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\ApiPlatform\DefaultValuesTrait;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSCreate;

class DefaultValuesTraitTest extends TestCase
{
    public function testDefaultValuesAreAppliedOnMissingProperties(): void
    {
        $operation = new CQRSCreate(defaultValues: ['active' => true, 'grade' => 0]);

        $this->assertEquals(
            ['name' => 'Carrier', 'active' => true, 'grade' => 0],
            $this->applyDefaultValues(['name' => 'Carrier'], $operation)
        );
    }

    public function testProvidedValuesAreNeverReplaced(): void
    {
        $operation = new CQRSCreate(defaultValues: ['active' => true, 'grade' => 0]);

        // An explicitly provided value is kept, even a falsy or a null one, since the client did provide it
        $this->assertEquals(
            ['active' => false, 'grade' => 0],
            $this->applyDefaultValues(['active' => false], $operation)
        );
        $this->assertEquals(
            ['active' => null, 'grade' => 0],
            $this->applyDefaultValues(['active' => null], $operation)
        );
    }

    public function testNonArrayInputIsReturnedAsIs(): void
    {
        $operation = new CQRSCreate(defaultValues: ['active' => true]);

        $this->assertEquals('input', $this->applyDefaultValues('input', $operation));
        $this->assertNull($this->applyDefaultValues(null, $operation));
    }

    public function testInputIsUntouchedWithoutOperation(): void
    {
        $this->assertEquals(['name' => 'Carrier'], $this->applyDefaultValues(['name' => 'Carrier'], null));
    }

    public function testInputIsUntouchedWhenOperationDeclaresNoDefaultValues(): void
    {
        $this->assertEquals(
            ['name' => 'Carrier'],
            $this->applyDefaultValues(['name' => 'Carrier'], new CQRSCreate())
        );
    }

    private function applyDefaultValues(mixed $input, ?Operation $operation): mixed
    {
        $applier = new class {
            use DefaultValuesTrait;

            public function apply(mixed $input, ?Operation $operation): mixed
            {
                return $this->applyDefaultValues($input, $operation);
            }
        };

        return $applier->apply($input, $operation);
    }
}
