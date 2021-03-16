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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;

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
     * @dataProvider getTestValues
     *
     * @param string $productType
     * @param bool $expectedSuppliers
     */
    public function testAdaptProductForm(string $productType, bool $expectedSuppliers): void
    {
        $listener = new ProductTypeListener();

        $form = $this->createForm(SimpleProductFormTest::class);
        $this->assertNotNull($form->get('suppliers'));

        $formData = [
            'basic' => [
                'type' => $productType,
            ],
        ];
        $eventMock = $this->createEventMock($formData, $form);
        $listener->adaptProductForm($eventMock);

        if ($expectedSuppliers) {
            $this->assertNotNull($form->get('suppliers'));
        } else {
            $this->expectException(OutOfBoundsException::class);
            $form->get('suppliers');
        }
    }

    public function getTestValues(): Generator
    {
        yield [
            ProductType::TYPE_STANDARD,
            true,
        ];

        yield [
            ProductType::TYPE_PACK,
            true,
        ];

        yield [
            ProductType::TYPE_VIRTUAL,
            true,
        ];

        yield [
            ProductType::TYPE_COMBINATION,
            false,
        ];
    }
}

class SimpleProductFormTest extends CommonAbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('suppliers', ChoiceType::class)
        ;
    }
}
