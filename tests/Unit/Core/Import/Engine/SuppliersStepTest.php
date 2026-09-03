<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Import\Engine;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductSupplierRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetProductDefaultSupplierCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\SetSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Supplier\Command\UpdateProductSuppliersCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder\FoundEntity;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Finder\SupplierFinder;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\Product\Step\SuppliersStep;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunOptions;
use PrestaShop\PrestaShop\Core\Import\Engine\ValueParser;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Exemplar seam test for a row step (the split's purpose: an applier's
 * contract reviewable alone). Pins the merge semantics that fixed the round-8
 * data-loss defects: the row's supplier is UNIONED with the existing
 * associations, and unmapped cells re-send the CURRENT reference/price/
 * currency instead of blanking them.
 */
class SuppliersStepTest extends TestCase
{
    private const PRODUCT_ID = 42;
    private const EXISTING_SUPPLIER_ID = 3;
    private const ROW_SUPPLIER_ID = 5;
    private const DEFAULT_CURRENCY_ID = 1;

    public function testTheRowSupplierIsUnionedWithTheExistingAssociations(): void
    {
        $step = $this->buildStep($commands, existingAssociations: [
            [
                'id_supplier' => self::EXISTING_SUPPLIER_ID,
                'id_currency' => 2,
                'product_supplier_reference' => 'KEEP-REF',
                'product_supplier_price_te' => '12.500000',
            ],
        ]);

        $messages = $step->apply(['supplier' => 'Acme'], 0, self::PRODUCT_ID, false, 1, $this->buildContext());

        $this->assertSame([], $messages);
        $this->assertCount(3, $commands);

        [$setSuppliers, $updateSuppliers, $setDefault] = $commands;
        $this->assertInstanceOf(SetSuppliersCommand::class, $setSuppliers);
        $supplierIds = array_map(static fn ($supplierId) => $supplierId->getValue(), $setSuppliers->getSupplierIds());
        $this->assertSame([self::EXISTING_SUPPLIER_ID, self::ROW_SUPPLIER_ID], $supplierIds, 'The existing supplier must survive the import');

        $this->assertInstanceOf(UpdateProductSuppliersCommand::class, $updateSuppliers);
        $this->assertInstanceOf(SetProductDefaultSupplierCommand::class, $setDefault);
        $this->assertSame(self::ROW_SUPPLIER_ID, $setDefault->getDefaultSupplierId()->getValue());
    }

    public function testUnmappedCellsReSendTheCurrentAssociationValues(): void
    {
        $step = $this->buildStep($commands, existingAssociations: [
            [
                'id_supplier' => self::ROW_SUPPLIER_ID,
                'id_currency' => 2,
                'product_supplier_reference' => 'KEEP-REF',
                'product_supplier_price_te' => '12.500000',
            ],
        ]);

        // the file maps only the supplier name: reference, price and currency
        // must keep their current values
        $step->apply(['supplier' => 'Acme'], 0, self::PRODUCT_ID, false, 1, $this->buildContext());

        $update = $this->updateCommand($commands)->getProductSuppliers()[0];
        $this->assertSame(2, $update->getCurrencyId()->getValue());
        $this->assertSame('KEEP-REF', $update->getReference());
        $this->assertSame('12.500000', $update->getPriceTaxExcluded());
    }

    public function testAFirstAssociationUsesTheFileValuesAndTheDefaultCurrency(): void
    {
        $step = $this->buildStep($commands, existingAssociations: []);

        $step->apply(
            ['supplier' => 'Acme', 'supplier_reference' => 'NEW-REF', 'wholesale_price' => '7.5'],
            0,
            self::PRODUCT_ID,
            false,
            1,
            $this->buildContext()
        );

        $update = $this->updateCommand($commands)->getProductSuppliers()[0];
        $this->assertSame(self::DEFAULT_CURRENCY_ID, $update->getCurrencyId()->getValue());
        $this->assertSame('NEW-REF', $update->getReference());
        $this->assertSame('7.5', $update->getPriceTaxExcluded());
    }

    public function testAnUnknownSupplierWarnsAndDispatchesNothing(): void
    {
        $step = $this->buildStep($commands, existingAssociations: [], match: new FoundEntity([]));

        $messages = $step->apply(['supplier' => 'Ghost'], 0, self::PRODUCT_ID, false, 1, $this->buildContext());

        $this->assertSame([], $commands, 'Suppliers are never auto-created');
        $this->assertCount(1, $messages);
        $this->assertSame(ImportMessage::SEVERITY_WARNING, $messages[0]->severity);
        $this->assertSame('supplier', $messages[0]->field);
    }

    /**
     * @param list<object>|null $commands captured dispatched commands, by reference
     * @param list<array<string, mixed>> $existingAssociations
     */
    private function buildStep(?array &$commands, array $existingAssociations, ?FoundEntity $match = null): SuppliersStep
    {
        $commands = [];

        $supplierFinder = $this->createMock(SupplierFinder::class);
        $supplierFinder->method('find')->willReturn(
            $match ?? new FoundEntity([['id' => self::ROW_SUPPLIER_ID, 'matchedBy' => FoundEntity::MATCHED_BY_NAME]])
        );

        $repository = $this->createMock(ProductSupplierRepository::class);
        $repository->method('getProductSuppliersInfo')->willReturn($existingAssociations);

        $configuration = $this->createMock(ShopConfigurationInterface::class);
        $configuration->method('get')->willReturn(self::DEFAULT_CURRENCY_ID);

        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->method('handle')->willReturnCallback(static function (object $command) use (&$commands) {
            $commands[] = $command;

            return null;
        });

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->method('trans')->willReturnCallback(
            static fn (string $id, array $parameters = []): string => strtr($id, $parameters)
        );

        return new SuppliersStep(
            new ValueParser(),
            $supplierFinder,
            $repository,
            $configuration,
            $commandBus,
            $translator,
        );
    }

    /**
     * @param list<object> $commands
     */
    private function updateCommand(array $commands): UpdateProductSuppliersCommand
    {
        foreach ($commands as $command) {
            if ($command instanceof UpdateProductSuppliersCommand) {
                return $command;
            }
        }

        $this->fail('No UpdateProductSuppliersCommand was dispatched');
    }

    private function buildContext(): ImportRunContext
    {
        return new ImportRunContext(
            'product',
            '/tmp/working-file.csv',
            10,
            'en',
            ',',
            [],
            ImportRunOptions::fromArray([]),
            ShopConstraint::shop(1)
        );
    }
}
