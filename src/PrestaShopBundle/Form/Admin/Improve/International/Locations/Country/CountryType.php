<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Locations\Country;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressFormat;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Country\Config\CountryConstraintConfiguration;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\ActiveZoneChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for country add/edit
 */
class CountryType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var FormChoiceProviderInterface
     */
    private $currencyChoiceProvider;

    /**
     * @var FeatureInterface
     */
    private $multistoreFeature;

    /**
     * @param TranslatorInterface $translator
     * @param FormChoiceProviderInterface $currencyChoiceProvider
     * @param FeatureInterface $multistoreFeature
     */
    public function __construct(
        TranslatorInterface $translator,
        FormChoiceProviderInterface $currencyChoiceProvider,
        FeatureInterface $multistoreFeature
    ) {
        $this->translator = $translator;
        $this->currencyChoiceProvider = $currencyChoiceProvider;
        $this->multistoreFeature = $multistoreFeature;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', TranslatableType::class, [
                'required' => true,
                'options' => [
                    'constraints' => [
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
                'attr' => [
                    'maxlength' => CountryConstraintConfiguration::MAX_ISO_CODE_LENGTH,
                ],
                'constraints' => [
                    new TypedRegex([
                        'type' => 'language_iso_code',
                    ]),
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'The %s field is required.',
                            [
                                sprintf('"%s"', $this->translator->trans(
                                    'ISO code', [], 'Admin.International.Feature'
                                )),
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new Length([
                        'max' => CountryConstraintConfiguration::MAX_ISO_CODE_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => CountryConstraintConfiguration::MAX_ISO_CODE_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('call_prefix', NumberType::class, [
                'required' => true,
                'attr' => [
                    'maxlength' => CountryConstraintConfiguration::MAX_CALL_PREFIX_LENGTH,
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'The %s field is required.',
                            [
                                sprintf('"%s"', $this->translator->trans(
                                    'Call prefix', [], 'Admin.International.Feature'
                                )),
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new Length([
                        'max' => CountryConstraintConfiguration::MAX_CALL_PREFIX_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => CountryConstraintConfiguration::MAX_CALL_PREFIX_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('default_currency', ChoiceType::class, [
                'required' => false,
                'choices' => array_merge(
                    [
                        $this->translator->trans(
                            'Default store currency',
                            [],
                            'Admin.International.Feature'
                        ) => 0,
                    ],
                    $this->currencyChoiceProvider->getChoices()
                ),
                'placeholder' => false,
            ])
            ->add('zone', ActiveZoneChoiceType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'The %s field is required.',
                            [
                                sprintf('"%s"', $this->translator->trans(
                                    'Zone', [], 'Admin.Global'
                                )),
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('need_zip_code', SwitchType::class, [
                'required' => false,
            ])
            ->add('zip_code_format', TextType::class, [
                'required' => false,
                'constraints' => [
                    new TypedRegex([
                        'type' => 'zip_code_format',
                    ]),
                ],
            ])
            ->add('address_format', TextareaType::class, [
                'required' => true,
                'constraints' => [
                    new AddressFormat(),
                ],
            ])
            ->add('is_enabled', SwitchType::class, [
                'required' => false,
                'data' => $this->getBoolValue($builder->getData(), 'is_enabled'),
            ])
            ->add('contains_states', SwitchType::class, [
                'required' => false,
            ])
            ->add('need_identification_number', SwitchType::class, [
                'required' => false,
            ])
            ->add('display_tax_label', SwitchType::class, [
                'required' => false,
                'data' => $this->getBoolValue($builder->getData(), 'display_tax_label'),
            ]);

        if ($this->multistoreFeature->isActive()) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'required' => false,
            ]);
        }
    }

    /**
     * Gets boolean value for edit form fields with default value "true" instead hardcoded data value
     *
     * @param array $data
     * @param string $index
     *
     * @return bool
     */
    private function getBoolValue(array $data, string $index): bool
    {
        return isset($data[$index]) && is_bool($data[$index]) ? $data[$index] : true;
    }
}
