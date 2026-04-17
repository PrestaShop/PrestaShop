<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Form\IdentifiableObject\DataProvider;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\Query\GetTaxRulesGroupForEditing;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\QueryResult\EditableTaxRulesGroup;
use PrestaShop\PrestaShop\Core\Domain\TaxRulesGroup\ValueObject\TaxRulesGroupId;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataProvider\TaxRulesGroupFormDataProvider;

class TaxRulesGroupFormDataProviderTest extends TestCase
{
    public function testGetDefaultData(): void
    {
        $formDataProvider = new TaxRulesGroupFormDataProvider($this->mockQueryBus());

        $this->assertEquals([], $formDataProvider->getDefaultData());
    }

    public function testGetData(): void
    {
        $formDataProvider = new TaxRulesGroupFormDataProvider($this->mockQueryBus());

        $this->assertEquals([
            'name' => 'My Tax Rules Group',
            'is_enabled' => true,
            'shop_association' => [1, 2],
        ], $formDataProvider->getData(2));
    }

    private function mockQueryBus(): CommandBusInterface
    {
        $queryBus = $this->createMock(CommandBusInterface::class);
        $queryBus
            ->method('handle')
            ->with($this->isInstanceOf(GetTaxRulesGroupForEditing::class))
            ->willReturn(
                new EditableTaxRulesGroup(
                    new TaxRulesGroupId(2),
                    'My Tax Rules Group',
                    true,
                    [1, 2]
                )
            )
        ;

        return $queryBus;
    }
}
