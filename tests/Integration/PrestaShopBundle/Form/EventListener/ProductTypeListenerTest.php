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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\EventListener;

use Generator;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use PrestaShopBundle\Form\Admin\Sell\Product\EventListener\ProductTypeListener;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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

    public function getFormTypeExpectationsBasedOnProductType(): Generator
    {
        yield [ProductType::TYPE_STANDARD, 'options.product_suppliers', true];
        yield [ProductType::TYPE_PACK, 'options.product_suppliers', true];
        yield [ProductType::TYPE_VIRTUAL, 'options.product_suppliers', true];
        yield [ProductType::TYPE_COMBINATIONS, 'options.product_suppliers', false];

        yield [ProductType::TYPE_STANDARD, 'stock', true];
        yield [ProductType::TYPE_PACK, 'stock', true];
        yield [ProductType::TYPE_VIRTUAL, 'stock', true];
        yield [ProductType::TYPE_COMBINATIONS, 'stock', false];

        yield [ProductType::TYPE_STANDARD, 'shipping', true];
        yield [ProductType::TYPE_PACK, 'shipping', true];
        yield [ProductType::TYPE_VIRTUAL, 'shipping', false];
        yield [ProductType::TYPE_COMBINATIONS, 'shipping', true];

        yield [ProductType::TYPE_STANDARD, 'stock.pack_stock_type', false];
        yield [ProductType::TYPE_PACK, 'stock.pack_stock_type', true];
        yield [ProductType::TYPE_VIRTUAL, 'stock.pack_stock_type', false];
        yield [ProductType::TYPE_COMBINATIONS, 'stock.pack_stock_type', false];

        yield [ProductType::TYPE_STANDARD, 'stock.virtual_product_file', false];
        yield [ProductType::TYPE_PACK, 'stock.virtual_product_file', false];
        yield [ProductType::TYPE_VIRTUAL, 'stock.virtual_product_file', true];
        yield [ProductType::TYPE_COMBINATIONS, 'stock.virtual_product_file', false];

        yield [ProductType::TYPE_STANDARD, 'combinations', false];
        yield [ProductType::TYPE_PACK, 'combinations', false];
        yield [ProductType::TYPE_VIRTUAL, 'combinations', false];
        yield [ProductType::TYPE_COMBINATIONS, 'combinations', true];

        yield [ProductType::TYPE_STANDARD, 'pricing.retail_price.ecotax_tax_excluded', true];
        yield [ProductType::TYPE_PACK, 'pricing.retail_price.ecotax_tax_excluded', true];
        yield [ProductType::TYPE_VIRTUAL, 'pricing.retail_price.ecotax_tax_excluded', false];
        yield [ProductType::TYPE_COMBINATIONS, 'pricing.retail_price.ecotax_tax_excluded', true];

        yield [ProductType::TYPE_STANDARD, 'pricing.retail_price.ecotax_tax_included', true];
        yield [ProductType::TYPE_PACK, 'pricing.retail_price.ecotax_tax_included', true];
        yield [ProductType::TYPE_VIRTUAL, 'pricing.retail_price.ecotax_tax_included', false];
        yield [ProductType::TYPE_COMBINATIONS, 'pricing.retail_price.ecotax_tax_included', true];
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

    public function getFormTypeSwitching(): Generator
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
     * @param FormInterface $form
     * @param string $productType
     * @param array $extraData
     */
    private function adaptProductFormBasedOnProductType(FormInterface $form, string $productType, array $extraData = []): void
    {
        $listener = new ProductTypeListener();

        $formData = empty($extraData) ? [] : $extraData;
        $formData['header']['type'] = $productType;

        $eventMock = $this->createEventMock($formData, $form);
        $listener->adaptProductForm($eventMock);
    }
}
