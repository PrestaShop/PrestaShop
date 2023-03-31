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

namespace PrestaShopBundle\Form\Admin\Improve\International\Locations;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressFormat;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

class CountryType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var bool
     */
    protected $isMultistoreEnabled;

    /**
     * @var FormChoiceProviderInterface
     */
    protected $currencyChoiceProvider;

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    protected $zoneChoiceProvider;

    /**
     * ZoneType constructor.
     */
    public function __construct(
        TranslatorInterface $translator,
        bool $isMultistoreEnabled,
        FormChoiceProviderInterface $currencyChoiceProvider,
        ConfigurableFormChoiceProviderInterface $zoneChoiceProvider
    ) {
        $this->translator = $translator;
        $this->isMultistoreEnabled = $isMultistoreEnabled;
        $this->currencyChoiceProvider = $currencyChoiceProvider;
        $this->zoneChoiceProvider = $zoneChoiceProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, [
                'label' => $this->translator->trans('Country', [], 'Admin.Global'),
                'options' => [
                    'help' => $this->translator->trans('Country name - Invalid characters: <>;=#{}'),
                    'constraints' => [
                        new Length([
                            'max' => 64,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters.',
                                ['%limit%' => 64],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                        new TypedRegex([
                            'type' => 'generic_name',
                        ]),
                    ],
                ],
                'constraints' => [
                    new DefaultLanguage(),
                ],
            ])
            ->add('iso_code', TextType::class, [
                'label' => $this->translator->trans('ISO code', [], 'Admin.Global'),
                'help' => $this->translator->trans('Two -- or three -- letter ISO code (e.g. "us" for United States).'),
                'required' => true,
                'constraints' => [
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_LANGUAGE_ISO_CODE,
                    ]),
                ],
            ])
            ->add('call_prefix', TextType::class, [
                'label' => $this->translator->trans('Call prefix', [], 'Admin.Global'),
                'required' => true,
                'help' => $this->translator->trans('International call prefix, (e.g. 1 for United States).'),
            ])
            ->add('default_currency', ChoiceType::class, [
                'label' => $this->translator->trans('Default currency', [], 'Admin.Global'),
                'required' => false,
                'choices' => $this->currencyChoiceProvider->getChoices(),
                'placeholder' => $this->translator->trans('Default store currency', [], 'Admin.International.Feature'),
            ])
            ->add('zone', ChoiceType::class, [
                'label' => $this->translator->trans('Zone', [], 'Admin.Global'),
                'required' => false,
                'help' => $this->translator->trans('Geographical region.'),
                'choices' => $this->zoneChoiceProvider->getChoices(
                    [
                        'active' => false,
                        'active_first' => false,
                    ]
                ),
                'placeholder' => false,
            ])
            ->add('need_zip_code', SwitchType::class, [
                'label' => $this->translator->trans('Does it need Zip/postal code?', [], 'Admin.Global'),
                'required' => false,
                'help' => $this->translator->trans("Indicate the format of the postal code: use L for a letter, N for a number, and C for the country's ISO 3166-1 alpha-2 code. For example, NNNNN for the United States, France, Poland and many other; LNNNNLLL for Argentina, etc. If you do not want PrestaShop to verify the postal code for this country, leave it blank."),
            ])
            ->add('zip_code_format', TextType::class, [
                'label' => $this->translator->trans('Zip/postal code format', [], 'Admin.Global'),
                'required' => true,
                'constraints' => [
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_ZIP_CODE_FORMAT,
                    ]),
                ],
            ])
            ->add('address_format', AddressFormatType::class, [
                'required' => false,
                'label' => false,
            ])
            ->add('is_enabled', SwitchType::class, [
                'label' => $this->translator->trans('Active', [], 'Admin.Global'),
                'required' => false,
                'help' => $this->translator->trans('Display this country to your customers (the selected country will always be displayed in the back office).'),
            ])
            ->add('contains_states', SwitchType::class, [
                'label' => $this->translator->trans('Contains states', [], 'Admin.Global'),
                'required' => false,
            ])
            ->add('need_identification_number', SwitchType::class, [
                'label' => $this->translator->trans('Do you need a tax identification number?', [], 'Admin.Global'),
                'required' => false,
            ])
            ->add('display_tax_label', SwitchType::class, [
                'label' => $this->translator->trans('', [], 'Admin.Global'),
                'required' => false,
            ]);

        if ($this->isMultistoreEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'label' => $this->translator->trans('Display tax label (e.g. "Tax incl.")', [], 'Admin.Global'),
                'required' => false,
            ]);
        }
    }

}
