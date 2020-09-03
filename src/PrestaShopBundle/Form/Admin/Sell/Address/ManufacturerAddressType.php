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

namespace PrestaShopBundle\Form\Admin\Sell\Address;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressDniRequired;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\AddressStateRequired;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Address\AddressSettings;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Defines form for address create/edit actions (Sell > Catalog > Brands & Suppliers)
 */
class ManufacturerAddressType extends AbstractType
{
    /**
     * @var array
     */
    private $manufacturerChoices;

    /**
     * @var array
     */
    private $countryChoices;

    /**
     * @var array
     */
    private $countryChoicesAttributes;

    /**
     * @var ConfigurableFormChoiceProviderInterface
     */
    private $statesChoiceProvider;

    /**
     * @var int
     */
    private $contextCountryId;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param array $manufacturerChoices
     * @param array $countryChoices
     * @param ConfigurableFormChoiceProviderInterface $statesChoiceProvider
     * @param int $contextCountryId
     * @param TranslatorInterface $translator
     * @param array $countryChoicesAttributes
     */
    public function __construct(
        array $manufacturerChoices,
        array $countryChoices,
        ConfigurableFormChoiceProviderInterface $statesChoiceProvider,
        $contextCountryId,
        TranslatorInterface $translator,
        array $countryChoicesAttributes
    ) {
        $this->manufacturerChoices = $manufacturerChoices;
        $this->countryChoices = $countryChoices;
        $this->statesChoiceProvider = $statesChoiceProvider;
        $this->contextCountryId = $contextCountryId;
        $this->translator = $translator;
        $this->countryChoicesAttributes = $countryChoicesAttributes;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();
        $countryId = 0 !== $data['id_country'] ? $data['id_country'] : $this->contextCountryId;
        $stateChoices = $this->statesChoiceProvider->getChoices(['id_country' => $countryId]);

        $builder
            ->add('id_manufacturer', ChoiceType::class, [
                'choices' => $this->getManufacturersChoiceList(),
                'translation_domain' => false,
                'placeholder' => false,
                'required' => false,
            ])
            ->add('last_name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new TypedRegex([
                        'type' => 'name',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_NAME_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => AddressSettings::MAX_NAME_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('first_name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new TypedRegex([
                        'type' => 'name',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_NAME_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => AddressSettings::MAX_NAME_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new TypedRegex([
                        'type' => 'address',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_ADDRESS_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => AddressSettings::MAX_ADDRESS_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('address2', TextType::class, [
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new TypedRegex([
                        'type' => 'address',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_ADDRESS_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => AddressSettings::MAX_ADDRESS_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('post_code', TextType::class, [
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new TypedRegex([
                        'type' => 'post_code',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_POST_CODE_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => AddressSettings::MAX_POST_CODE_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                    new TypedRegex([
                        'type' => 'city_name',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_CITY_NAME_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => AddressSettings::MAX_CITY_NAME_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('id_country', CountryChoiceType::class, [
                'required' => true,
                'with_dni_attr' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('id_state', ChoiceType::class, [
                'required' => true,
                'choices' => $stateChoices,
                'constraints' => [
                    new AddressStateRequired([
                        'id_country' => $countryId,
                    ]),
                ],
            ])
            ->add('home_phone', TextType::class, [
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new TypedRegex([
                        'type' => 'phone_number',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_PHONE_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => AddressSettings::MAX_PHONE_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('mobile_phone', TextType::class, [
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new TypedRegex([
                        'type' => 'phone_number',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_PHONE_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => AddressSettings::MAX_PHONE_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('dni', TextType::class, [
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new AddressDniRequired([
                        'required' => false,
                        'id_country' => $countryId,
                    ]),
                    new TypedRegex([
                        'type' => 'dni_lite',
                    ]),
                    new Length([
                        'max' => 16,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => 16],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('other', TextType::class, [
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new TypedRegex([
                        'type' => 'message',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_OTHER_LENGTH,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => AddressSettings::MAX_OTHER_LENGTH],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
        ;
    }

    /**
     * Get manufacturers array for choice list
     * List is modified to enable selecting -no manufacturer-
     *
     * @return array
     */
    private function getManufacturersChoiceList()
    {
        $this->manufacturerChoices['--'] = 0;

        return $this->manufacturerChoices;
    }
}
