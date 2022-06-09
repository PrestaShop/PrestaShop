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
                'options' => [
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
                'required' => true,
                'constraints' => [
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_LANGUAGE_ISO_CODE,
                    ]),
                ],
            ])
            ->add('call_prefix', TextType::class, [
                'required' => true,
            ])
            ->add('default_currency', ChoiceType::class, [
                'required' => false,
                'choices' => $this->currencyChoiceProvider->getChoices(),
                'placeholder' => $this->translator->trans('Default store currency', [], 'Admin.International.Feature'),
            ])
            ->add('zone', ChoiceType::class, [
                'required' => false,
                'choices' => $this->zoneChoiceProvider->getChoices(
                    [
                        'active' => false,
                        'active_first' => false,
                    ]
                ),
                'placeholder' => false,
            ])
            ->add('need_zip_code', SwitchType::class, [
                'required' => false,
            ])
            ->add('zip_code_format', TextType::class, [
                'required' => true,
                'constraints' => [
                    new TypedRegex([
                        'type' => TypedRegex::TYPE_ZIP_CODE_FORMAT,
                    ]),
                ],
            ])
            ->add('address_format', TextareaType::class, [
                'required' => false,
                'constraints' => [
                    new AddressFormat(),
                ],
            ])
            ->add('is_enabled', SwitchType::class, [
                'required' => false,
            ])
            ->add('contains_states', SwitchType::class, [
                'required' => false,
            ])
            ->add('need_identification_number', SwitchType::class, [
                'required' => false,
            ])
            ->add('display_tax_label', SwitchType::class, [
                'required' => false,
            ]);

        if ($this->isMultistoreEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'required' => false,
            ]);
        }
    }
}
