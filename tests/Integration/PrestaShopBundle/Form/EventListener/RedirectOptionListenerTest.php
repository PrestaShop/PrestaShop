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
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\RedirectType;
use PrestaShopBundle\Form\Admin\Sell\Product\EventListener\RedirectOptionListener;
use PrestaShopBundle\Form\Admin\Type\CommonAbstractType;
use PrestaShopBundle\Form\Admin\Type\EntitySearchInputType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class RedirectOptionListenerTest extends FormListenerTestCase
{
    public function testSubscribedEvents(): void
    {
        // Only events are relevant, the matching function is up to implementation
        $expectedSubscribedEvents = [
            FormEvents::PRE_SET_DATA,
            FormEvents::PRE_SUBMIT,
        ];
        $subscribedEvents = RedirectOptionListener::getSubscribedEvents();
        $this->assertSame($expectedSubscribedEvents, array_keys($subscribedEvents));
    }

    /**
     * @dataProvider getExpectedOptionsBasedOnData
     */
    public function testTargetOptionsBasedOnData(string $redirectionType, array $expectedOptions): void
    {
        $form = $this->createForm(SimpleTargetFormTest::class);
        $this->adaptRedirectOptions($form, ['type' => $redirectionType]);

        $targetForm = $form->get('target');
        foreach ($expectedOptions as $optionName => $expectedValue) {
            $this->assertEquals($expectedValue, $targetForm->getConfig()->getOption($optionName));
        }
    }

    public function getExpectedOptionsBasedOnData(): Generator
    {
        yield [
            RedirectType::TYPE_CATEGORY_PERMANENT,
            [
                'label' => SimpleTargetFormTest::CATEGORY_LABEL,
                'placeholder' => SimpleTargetFormTest::CATEGORY_PLACEHOLDER,
                'help' => SimpleTargetFormTest::CATEGORY_HELP,
                'remote_url' => SimpleTargetFormTest::CATEGORY_SEARCH_URL,
            ],
        ];

        yield [
            RedirectType::TYPE_CATEGORY_TEMPORARY,
            [
                'label' => SimpleTargetFormTest::CATEGORY_LABEL,
                'placeholder' => SimpleTargetFormTest::CATEGORY_PLACEHOLDER,
                'help' => SimpleTargetFormTest::CATEGORY_HELP,
                'remote_url' => SimpleTargetFormTest::CATEGORY_SEARCH_URL,
            ],
        ];

        yield [
            RedirectType::TYPE_PRODUCT_PERMANENT,
            [
                'label' => SimpleTargetFormTest::PRODUCT_LABEL,
                'placeholder' => SimpleTargetFormTest::PRODUCT_PLACEHOLDER,
                'help' => SimpleTargetFormTest::PRODUCT_HELP,
                'remote_url' => SimpleTargetFormTest::PRODUCT_SEARCH_URL,
            ],
        ];

        yield [
            RedirectType::TYPE_PRODUCT_TEMPORARY,
            [
                'label' => SimpleTargetFormTest::PRODUCT_LABEL,
                'placeholder' => SimpleTargetFormTest::PRODUCT_PLACEHOLDER,
                'help' => SimpleTargetFormTest::PRODUCT_HELP,
                'remote_url' => SimpleTargetFormTest::PRODUCT_SEARCH_URL,
            ],
        ];

        // Default is product
        yield [
            RedirectType::TYPE_NOT_FOUND,
            [
                'label' => SimpleTargetFormTest::PRODUCT_LABEL,
                'placeholder' => SimpleTargetFormTest::PRODUCT_PLACEHOLDER,
                'help' => SimpleTargetFormTest::PRODUCT_HELP,
                'remote_url' => SimpleTargetFormTest::PRODUCT_SEARCH_URL,
                'row_attr' => [
                    'class' => 'd-none',
                ],
            ],
        ];
    }

    /**
     * @param FormInterface $form
     * @param array $formData
     */
    private function adaptRedirectOptions(FormInterface $form, array $formData): void
    {
        $listener = new RedirectOptionListener();

        $eventMock = $this->createEventMock($formData, $form);
        $listener->updateRedirectionOptions($eventMock);
    }
}

class SimpleTargetFormTest extends CommonAbstractType
{
    public const PRODUCT_LABEL = 'Product label';
    public const PRODUCT_PLACEHOLDER = 'Product placeholder';
    public const PRODUCT_SEARCH_URL = 'Product search url';
    public const PRODUCT_HELP = 'Product help';

    public const CATEGORY_LABEL = 'Category label';
    public const CATEGORY_PLACEHOLDER = 'Category placeholder';
    public const CATEGORY_SEARCH_URL = 'Category search url';
    public const CATEGORY_HELP = 'Category help';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', FormType::class)
            ->add('target', EntitySearchInputType::class, [
                'attr' => [
                    'data-product-label' => static::PRODUCT_LABEL,
                    'data-product-placeholder' => static::PRODUCT_PLACEHOLDER,
                    'data-product-search-url' => static::PRODUCT_SEARCH_URL,
                    'data-product-help' => static::PRODUCT_HELP,
                    'data-category-label' => static::CATEGORY_LABEL,
                    'data-category-placeholder' => static::CATEGORY_PLACEHOLDER,
                    'data-category-help' => static::CATEGORY_HELP,
                    'data-category-search-url' => static::CATEGORY_SEARCH_URL,
                ],
            ])
        ;
    }
}
