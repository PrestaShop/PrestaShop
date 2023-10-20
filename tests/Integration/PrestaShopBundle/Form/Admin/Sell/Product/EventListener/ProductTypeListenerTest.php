<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Sell\Product\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PrestaShop\PrestaShop\Adapter\Hook\HookInformationProvider;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShopBundle\Form\Admin\Sell\Product\EventListener\ProductTypeListener;
use PrestaShopBundle\Form\Admin\Sell\Product\ExtraModulesType;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Tests\Integration\PrestaShopBundle\Form\FormListenerTestCase;
use Tests\Integration\PrestaShopBundle\Form\TestProductFormType;

class ProductTypeListenerTest extends FormListenerTestCase
{
    public function testSubscribedEvents(): void
    {
        // Only events are relevant, the matching function is up to implementation
        $expectedSubscribedEvents = [
            FormEvents::PRE_SET_DATA,
            FormEvents::PRE_SUBMIT,
        ];
        $subscribedEvents = ProductTypeListener::getSubscribedEvents();
        $this->assertSame($expectedSubscribedEvents, array_keys($subscribedEvents));
    }

    /**
     * @dataProvider getFormTypeExpectationsBasedOnProductType
     *
     * @param string $productType
     * @param string $formTypeName
     * @param bool $shouldExist
     */
    public function testFormTypeExistsInFormDependingOnProductType(string $productType, string $formTypeName, bool $shouldExist): void
    {
        $form = $this->createForm(TestProductFormType::class);
        $this->assertFormTypeExistsInForm($form, $formTypeName, true);
        $this->adaptProductFormBasedOnProductType($form, $productType);
        $this->assertFormTypeExistsInForm($form, $formTypeName, $shouldExist);
    }

    public function getFormTypeExpectationsBasedOnProductType(): iterable
    {
        // Since data is empty all product types remove this field see testSuppliers for more details
        yield 'product_supplier in standard context' => [ProductType::TYPE_STANDARD, 'options.product_suppliers', false];
        yield 'product_supplier in pack context' => [ProductType::TYPE_PACK, 'options.product_suppliers', false];
        yield 'product_supplier in virtual context' => [ProductType::TYPE_VIRTUAL, 'options.product_suppliers', false];
        yield 'product_supplier in combination context' => [ProductType::TYPE_COMBINATIONS, 'options.product_suppliers', false];

        yield 'stock in standard context' => [ProductType::TYPE_STANDARD, 'stock', true];
        yield 'stock in pack context' => [ProductType::TYPE_PACK, 'stock', true];
        yield 'stock in combination context' => [ProductType::TYPE_VIRTUAL, 'stock', true];
        yield 'stock in virtual context' => [ProductType::TYPE_COMBINATIONS, 'stock', false];

        yield 'shipping in standard context' => [ProductType::TYPE_STANDARD, 'shipping', true];
        yield 'shipping in pack context' => [ProductType::TYPE_PACK, 'shipping', true];
        yield 'shipping in combination context' => [ProductType::TYPE_VIRTUAL, 'shipping', false];
        yield 'shipping in virtual context' => [ProductType::TYPE_COMBINATIONS, 'shipping', true];

        yield 'pack in standard context' => [ProductType::TYPE_STANDARD, 'stock.packed_products', false];
        yield 'pack in pack context' => [ProductType::TYPE_PACK, 'stock.packed_products', true];
        yield 'pack in combination context' => [ProductType::TYPE_COMBINATIONS, 'stock.packed_products', false];
        yield 'pack in virtual context' => [ProductType::TYPE_VIRTUAL, 'stock.packed_products', false];

        yield 'pack_stock_type in standard context' => [ProductType::TYPE_STANDARD, 'stock.pack_stock_type', false];
        yield 'pack_stock_type in pack context' => [ProductType::TYPE_PACK, 'stock.pack_stock_type', true];
        yield 'pack_stock_type in combination context' => [ProductType::TYPE_VIRTUAL, 'stock.pack_stock_type', false];
        yield 'pack_stock_type in virtual context' => [ProductType::TYPE_COMBINATIONS, 'stock.pack_stock_type', false];

        yield 'virtual_product_file in standard context' => [ProductType::TYPE_STANDARD, 'stock.virtual_product_file', false];
        yield 'virtual_product_file in pack context' => [ProductType::TYPE_PACK, 'stock.virtual_product_file', false];
        yield 'virtual_product_file in combination context' => [ProductType::TYPE_VIRTUAL, 'stock.virtual_product_file', true];
        yield 'virtual_product_file in virtual context' => [ProductType::TYPE_COMBINATIONS, 'stock.virtual_product_file', false];

        yield 'combinations in standard context' => [ProductType::TYPE_STANDARD, 'combinations', false];
        yield 'combinations in pack context' => [ProductType::TYPE_PACK, 'combinations', false];
        yield 'combinations in virtual context' => [ProductType::TYPE_VIRTUAL, 'combinations', false];
        yield 'combinations in combination context' => [ProductType::TYPE_COMBINATIONS, 'combinations', true];

        yield 'ecotax_tax_excluded in standard context' => [ProductType::TYPE_STANDARD, 'pricing.retail_price.ecotax_tax_excluded', true];
        yield 'ecotax_tax_excluded in pack context' => [ProductType::TYPE_PACK, 'pricing.retail_price.ecotax_tax_excluded', true];
        yield 'ecotax_tax_excluded in virtual context' => [ProductType::TYPE_VIRTUAL, 'pricing.retail_price.ecotax_tax_excluded', false];
        yield 'ecotax_tax_excluded in combination context' => [ProductType::TYPE_COMBINATIONS, 'pricing.retail_price.ecotax_tax_excluded', true];

        yield 'ecotax_tax_included in standard context' => [ProductType::TYPE_STANDARD, 'pricing.retail_price.ecotax_tax_included', true];
        yield 'ecotax_tax_included in pack context' => [ProductType::TYPE_PACK, 'pricing.retail_price.ecotax_tax_included', true];
        yield 'ecotax_tax_included in virtual context' => [ProductType::TYPE_VIRTUAL, 'pricing.retail_price.ecotax_tax_included', false];
        yield 'ecotax_tax_included in combination context' => [ProductType::TYPE_COMBINATIONS, 'pricing.retail_price.ecotax_tax_included', true];
    }

    /**
     * When type is switched, the removed fields are not the same but the adapt function is called twice in the same
     * request (PRE_SET_DATA and PRE_SUBMIT) so we must be sure that it doesn't create error because of fields no
     * present any more
     *
     * @dataProvider getFormTypeSwitching
     *
     * @param string $initialProductType
     * @param string $newProductType
     */
    public function testFormTypeSwitching(string $initialProductType, string $newProductType): void
    {
        $form = $this->createForm(TestProductFormType::class);
        $this->adaptProductFormBasedOnProductType($form, $initialProductType);
        $this->adaptProductFormBasedOnProductType($form, $newProductType);
    }

    public function getFormTypeSwitching(): iterable
    {
        $productTypes = [
            ProductType::TYPE_STANDARD,
            ProductType::TYPE_COMBINATIONS,
            ProductType::TYPE_VIRTUAL,
            ProductType::TYPE_PACK,
        ];

        foreach ($productTypes as $initialProductType) {
            foreach ($productTypes as $newProductType) {
                yield [$initialProductType, $newProductType];
            }
        }
    }

    /**
     * @dataProvider getStockMovements
     *
     * @param string $productType
     * @param array $movementsData
     * @param bool $shouldExist
     */
    public function testStockMovementsRemovedBasedOnItsContent(string $productType, array $movementsData, bool $shouldExist): void
    {
        $formData = [
            'stock' => [
                'quantities' => [
                    'stock_movements' => $movementsData,
                ],
            ],
        ];
        $form = $this->createForm(TestProductFormType::class, [], $formData);

        $this->assertFormTypeExistsInForm($form, 'stock.quantities.stock_movements', true);
        $this->adaptProductFormBasedOnProductType($form, $productType, $formData);
        $this->assertFormTypeExistsInForm($form, 'stock.quantities.stock_movements', $shouldExist);
    }

    public function getStockMovements(): iterable
    {
        yield [ProductType::TYPE_STANDARD, [], false];
        yield [ProductType::TYPE_COMBINATIONS, [], false];
        yield [ProductType::TYPE_PACK, [], false];
        yield [ProductType::TYPE_VIRTUAL, [], false];

        $stockMovements = [
            [
                'employee' => 'John Doe',
                'delta_quantity' => 42,
            ],
            [
                'employee' => 'John Doe',
                'delta_quantity' => -15,
            ],
        ];

        yield [ProductType::TYPE_STANDARD, $stockMovements, true];
        yield [ProductType::TYPE_COMBINATIONS, $stockMovements, false];
        yield [ProductType::TYPE_PACK, $stockMovements, true];
        yield [ProductType::TYPE_VIRTUAL, $stockMovements, true];
    }

    /**
     * @dataProvider getVirtualData
     *
     * @param array $formData
     * @param bool $ecotaxExpected
     */
    public function testEcotaxForVirtualProduct(array $formData, bool $ecotaxExpected): void
    {
        $form = $this->createForm(TestProductFormType::class, [], $formData);

        $this->assertFormTypeExistsInForm($form, 'pricing.retail_price.ecotax_tax_excluded', true);
        $this->assertFormTypeExistsInForm($form, 'pricing.retail_price.ecotax_tax_included', true);
        $this->adaptProductFormBasedOnProductType($form, $formData['header']['type'], $formData);
        $this->assertFormTypeExistsInForm($form, 'pricing.retail_price.ecotax_tax_excluded', $ecotaxExpected);
        $this->assertFormTypeExistsInForm($form, 'pricing.retail_price.ecotax_tax_included', $ecotaxExpected);
    }

    public function getVirtualData(): iterable
    {
        yield 'no initial type defined, virtual type defined, ecotax removed' => [
            [
                'header' => [
                    'type' => ProductType::TYPE_VIRTUAL,
                ],
            ],
            false,
        ];

        yield 'both initial and current are virtual, ecotax removed' => [
            [
                'header' => [
                    'type' => ProductType::TYPE_VIRTUAL,
                    'initial_type' => ProductType::TYPE_VIRTUAL,
                ],
            ],
            false,
        ];

        yield 'initial standard and current virtual, ecotax present' => [
            [
                'header' => [
                    'type' => ProductType::TYPE_VIRTUAL,
                    'initial_type' => ProductType::TYPE_STANDARD,
                ],
            ],
            true,
        ];

        yield 'initial virtual and current standard, ecotax present' => [
            [
                'header' => [
                    'type' => ProductType::TYPE_VIRTUAL,
                    'initial_type' => ProductType::TYPE_STANDARD,
                ],
            ],
            true,
        ];
    }

    /**
     * @dataProvider getExpectedSuppliers
     *
     * @param string $formType
     * @param array $suppliers
     * @param bool $suppliersExpected
     * @param bool $productSuppliersExpected
     */
    public function testSuppliers(string $formType, array $suppliers, bool $suppliersExpected, bool $productSuppliersExpected): void
    {
        $form = $this->createForm(TestProductFormType::class, ['suppliers' => $suppliers]);

        $this->assertFormTypeExistsInForm($form, 'options.suppliers', true);
        $this->assertFormTypeExistsInForm($form, 'options.product_suppliers', true);
        $this->adaptProductFormBasedOnProductType($form, $formType);
        $this->assertFormTypeExistsInForm($form, 'options.suppliers', $suppliersExpected);
        $this->assertFormTypeExistsInForm($form, 'options.product_suppliers', $productSuppliersExpected);
    }

    public function getExpectedSuppliers(): iterable
    {
        yield 'standard type with suppliers' => [ProductType::TYPE_STANDARD, [42, 69], true, true];
        yield 'virtual type with suppliers' => [ProductType::TYPE_VIRTUAL, [42, 69], true, true];
        yield 'pack type with suppliers' => [ProductType::TYPE_PACK, [42, 69], true, true];
        yield 'combinations type with suppliers' => [ProductType::TYPE_COMBINATIONS, [42, 69], true, false];

        yield 'standard type without suppliers' => [ProductType::TYPE_STANDARD, [], false, false];
        yield 'virtual type without suppliers' => [ProductType::TYPE_VIRTUAL, [], false, false];
        yield 'pack type without suppliers' => [ProductType::TYPE_PACK, [], false, false];
        yield 'combination type without suppliers' => [ProductType::TYPE_COMBINATIONS, [], false, false];
    }

    /**
     * @dataProvider getProductTypes
     *
     * @param string $productType
     */
    public function testExtraModules(string $productType): void
    {
        $form = $this->createForm(TestProductFormType::class);

        $this->assertFormTypeExistsInForm($form, 'extra_modules', true);
        $this->adaptProductFormBasedOnProductType($form, $productType);
        $this->assertFormTypeExistsInForm($form, 'extra_modules', false);

        $form = $this->createForm(TestProductFormType::class);
        $this->assertFormTypeExistsInForm($form, 'extra_modules', true);
        $this->adaptProductFormBasedOnProductType($form, $productType, [], [
            [
                'module' => 'test',
            ],
        ]);
        $this->assertFormTypeExistsInForm($form, 'extra_modules', true);
    }

    public function getProductTypes(): iterable
    {
        yield 'combinations product' => [ProductType::TYPE_COMBINATIONS];
        yield 'standard product' => [ProductType::TYPE_STANDARD];
        yield 'pack product' => [ProductType::TYPE_PACK];
        yield 'virtual product' => [ProductType::TYPE_VIRTUAL];
    }

    /**
     * @param FormInterface $form
     * @param string $productType
     * @param array $extraData
     * @param array $registeredModules
     */
    private function adaptProductFormBasedOnProductType(FormInterface $form, string $productType, array $extraData = [], array $registeredModules = []): void
    {
        $listener = new ProductTypeListener($this->buildHookInformationProvider($registeredModules));

        $formData = empty($extraData) ? [] : $extraData;
        $formData['header']['type'] = $productType;

        $eventMock = $this->createEventMock($formData, $form);
        $listener->adaptProductForm($eventMock);
    }

    /**
     * @param array $registeredModules
     *
     * @return mixed|MockObject|HookInformationProvider
     */
    private function buildHookInformationProvider(array $registeredModules = [])
    {
        $hookInformationProvider = $this->getMockBuilder(HookInformationProvider::class)
            ->disableOriginalConstructor()
            ->getMock();
        $hookInformationProvider
            ->method('getRegisteredModulesByHookName')
            ->with(ExtraModulesType::HOOK_NAME)
            ->willReturn($registeredModules)
        ;

        return $hookInformationProvider;
    }
}
