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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Stock;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\DatePickerType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

class AvailabilityType extends TranslatorAwareType
{
    /**
     * @var FormChoiceProviderInterface
     */
    private $outOfStockTypeChoiceProvider;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param FormChoiceProviderInterface $outOfStockTypeChoiceProvider
     * @param RouterInterface $router
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        FormChoiceProviderInterface $outOfStockTypeChoiceProvider,
        RouterInterface $router
    ) {
        parent::__construct($translator, $locales);
        $this->outOfStockTypeChoiceProvider = $outOfStockTypeChoiceProvider;
        $this->router = $router;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('out_of_stock_type', ChoiceType::class, [
                'choices' => $this->outOfStockTypeChoiceProvider->getChoices(),
                'label' => false,
                'expanded' => true,
                'column_breaker' => true,
                'modify_all_shops' => true,
                'external_link' => [
                    'text' => $this->trans('[1]Edit default behavior[/1]', 'Admin.Catalog.Feature'),
                    'href' => $this->router->generate('admin_product_preferences') . '#configuration_fieldset_stock',
                ],
            ])
            ->add('available_now_label', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans('Label when in stock', 'Admin.Catalog.Feature'),
                'required' => false,
                'modify_all_shops' => true,
                'options' => [
                    'constraints' => [
                        new TypedRegex(TypedRegex::TYPE_GENERIC_NAME),
                        new Length([
                            'max' => ProductSettings::MAX_AVAILABLE_NOW_LABEL_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                ['%limit%' => ProductSettings::MAX_AVAILABLE_NOW_LABEL_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('available_later_label', TranslatableType::class, [
                'type' => TextType::class,
                'label' => $this->trans(
                    'Label when out of stock (and backorders allowed)',
                    'Admin.Catalog.Feature'
                ),
                'required' => false,
                'modify_all_shops' => true,
                'options' => [
                    'constraints' => [
                        new TypedRegex(TypedRegex::TYPE_GENERIC_NAME),
                        new Length([
                            'max' => ProductSettings::MAX_AVAILABLE_LATER_LABEL_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                ['%limit%' => ProductSettings::MAX_AVAILABLE_LATER_LABEL_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('available_date', DatePickerType::class, [
                'label' => $this->trans('Availability date', 'Admin.Catalog.Feature'),
                'required' => false,
                'attr' => [
                    'placeholder' => 'YYYY-MM-DD',
                ],
                'modify_all_shops' => true,
            ])
        ;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('When out of stock', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h3',
            'required' => false,
            'columns_number' => 3,
        ]);
    }
}
