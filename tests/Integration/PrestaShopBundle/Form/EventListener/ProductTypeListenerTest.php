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
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use Symfony\Component\Form\Exception\OutOfBoundsException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

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
        $form = $this->createForm(SimpleProductFormTest::class);
        $this->assertFormTypeExistsInForm($form, $formTypeName, true);
        $this->adaptProductFormBasedOnProductType($form, $productType);
        $this->assertFormTypeExistsInForm($form, $formTypeName, $shouldExist);
    }

    public function getFormTypeExpectationsBasedOnProductType(): Generator
    {
        yield [ProductType::TYPE_STANDARD, 'suppliers', true];
        yield [ProductType::TYPE_PACK, 'suppliers', true];
        yield [ProductType::TYPE_VIRTUAL, 'suppliers', true];
        yield [ProductType::TYPE_COMBINATIONS, 'suppliers', false];

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

        yield [ProductType::TYPE_STANDARD, 'shortcuts.stock', true];
        yield [ProductType::TYPE_PACK, 'shortcuts.stock', true];
        yield [ProductType::TYPE_VIRTUAL, 'shortcuts.stock', true];
        yield [ProductType::TYPE_COMBINATIONS, 'shortcuts.stock', false];
    }

    /**
     * @param FormInterface $form
     * @param string $productType
     */
    private function adaptProductFormBasedOnProductType(FormInterface $form, string $productType): void
    {
        $listener = new ProductTypeListener();

        $formData = [
            'header' => [
                'type' => $productType,
            ],
        ];
        $eventMock = $this->createEventMock($formData, $form);
        $listener->adaptProductForm($eventMock);
    }

    /**
     * @param FormInterface $form
     * @param string $typeName
     * @param bool $shouldExist
     */
    private function assertFormTypeExistsInForm(FormInterface $form, string $typeName, bool $shouldExist): void
    {
        if ($shouldExist) {
            $this->assertNotNull($this->getFormChild($form, $typeName));
        } else {
            $this->expectException(OutOfBoundsException::class);
            $this->getFormChild($form, $typeName);
        }
    }

    /**
     * @param FormInterface $form
     * @param string $typeName
     *
     * @return FormInterface
     */
    private function getFormChild(FormInterface $form, string $typeName): FormInterface
    {
        $typeNames = explode('.', $typeName);
        $child = $form;
        foreach ($typeNames as $typeName) {
            $child = $child->get($typeName);
        }

        return $child;
    }
}

class SimpleProductFormTest extends CommonAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('suppliers', ChoiceType::class)
            ->add('stock', FormType::class)
            ->add('shipping', FormType::class)
            ->add('shortcuts', FormType::class)
        ;
        $builder->get('shortcuts')->add('stock', FormType::class);
        $builder->get('stock')->add('pack_stock_type', ChoiceType::class);
        $builder->get('stock')->add('virtual_product_file', FormType::class);
    }
}
