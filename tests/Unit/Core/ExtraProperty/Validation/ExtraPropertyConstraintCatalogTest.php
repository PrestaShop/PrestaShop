<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\ExtraProperty\Validation;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintCatalog;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyConstraintMapper;

class ExtraPropertyConstraintCatalogTest extends TestCase
{
    public function testTheCatalogDescribesEveryWhitelistedConstraint(): void
    {
        $catalog = (new ExtraPropertyConstraintCatalog())->getCatalog();

        $this->assertSame(ExtraPropertyConstraintMapper::getAllowedNames(), array_keys($catalog));
        foreach ($catalog as $name => $entry) {
            $this->assertArrayHasKey('defaultOption', $entry, $name);
            $this->assertArrayHasKey('composite', $entry, $name);
            $this->assertArrayHasKey('required', $entry, $name);
            $this->assertArrayHasKey('options', $entry, $name);
        }
    }

    public function testCompositesAreFlagged(): void
    {
        $catalog = (new ExtraPropertyConstraintCatalog())->getCatalog();

        $this->assertTrue($catalog['All']['composite']);
        $this->assertTrue($catalog['Collection']['composite']);
        $this->assertFalse($catalog['NotBlank']['composite']);
        $this->assertFalse($catalog['Choice']['composite']);
    }

    public function testCuratedConstraintsExposeOrderedTypedOptions(): void
    {
        $catalog = (new ExtraPropertyConstraintCatalog())->getCatalog();

        $this->assertSame(
            ['min' => ['type' => 'int'], 'max' => ['type' => 'int'], 'charset' => ['type' => 'string']],
            $catalog['Length']['options']
        );
        $this->assertSame('list', $catalog['Choice']['options']['choices']['type']);
    }

    public function testDefaultOptionsAreReported(): void
    {
        $catalog = (new ExtraPropertyConstraintCatalog())->getCatalog();

        $this->assertSame('choices', $catalog['Choice']['defaultOption']);
        $this->assertSame('value', $catalog['GreaterThan']['defaultOption']);
        $this->assertNull($catalog['NotBlank']['defaultOption']);
    }

    public function testRequiredOptionsAreReported(): void
    {
        $catalog = (new ExtraPropertyConstraintCatalog())->getCatalog();

        $this->assertSame(['type'], $catalog['TypedRegex']['required']);
        $this->assertSame([], $catalog['NotBlank']['required']);
    }

    public function testReflectedOptionsSkipMessagesGroupsAndPayload(): void
    {
        $catalog = (new ExtraPropertyConstraintCatalog())->getCatalog();

        // NotBlank has no curated override: its options come from reflection.
        $options = $catalog['NotBlank']['options'];
        $this->assertArrayNotHasKey('message', $options);
        $this->assertArrayNotHasKey('groups', $options);
        $this->assertArrayNotHasKey('payload', $options);
        $this->assertArrayHasKey('allowNull', $options);
    }

    public function testTheCatalogIsJsonSerializable(): void
    {
        $json = json_encode((new ExtraPropertyConstraintCatalog())->getCatalog());

        $this->assertIsString($json);
        $this->assertJson($json);
    }
}
