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
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use PrestaShopBundle\Service\Routing\Router;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Defines form for address create/edit actions (Sell > Catalog > Brands & Suppliers)
 */
class ManufacturerAddressType extends TranslatorAwareType
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
     * @var Router
     */
    private $router;

    /**
     * @param array $manufacturerChoices
     * @param array $countryChoices
     * @param ConfigurableFormChoiceProviderInterface $statesChoiceProvider
     * @param int $contextCountryId
     * @param TranslatorInterface $translator
     * @param array $countryChoicesAttributes
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        array $manufacturerChoices,
        array $countryChoices,
        ConfigurableFormChoiceProviderInterface $statesChoiceProvider,
        $contextCountryId,
        array $countryChoicesAttributes,
        Router $router
    ) {
        parent::__construct($translator, $locales);
        $this->manufacturerChoices = $manufacturerChoices;
        $this->countryChoices = $countryChoices;
        $this->statesChoiceProvider = $statesChoiceProvider;
        $this->contextCountryId = $contextCountryId;
        $this->countryChoicesAttributes = $countryChoicesAttributes;
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $nameHint = $this->trans('Invalid characters:', 'Admin.Global') . ' 0-9!<>,;?=+()@#"�{}_$%:';
        $data = $builder->getData();
        $countryId = 0 !== $data['id_country'] ? $data['id_country'] : $this->contextCountryId;
        $stateChoices = $this->statesChoiceProvider->getChoices(['id_country' => $countryId]);
        $otherHint = $this->trans('Invalid characters:', 'Admin.Global') . ' <>{}';

        $builder
            ->add('id_manufacturer', ChoiceType::class, [
                'label' => $this->trans('Brand', 'Admin.Catalog.Feature'),
                'choices' => $this->getManufacturersChoiceList(),
                'translation_domain' => false,
                'placeholder' => false,
                'required' => false,
            ])
            ->add('last_name', TextType::class, [
                'label' => $this->trans('Last name', 'Admin.Global'),
                'help' => $nameHint,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                    new TypedRegex([
                        'type' => 'name',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_NAME_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressSettings::MAX_NAME_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('first_name', TextType::class, [
                'label' => $this->trans('First name', 'Admin.Global'),
                'help' => $nameHint,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                    new TypedRegex([
                        'type' => 'name',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_NAME_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressSettings::MAX_NAME_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'label' => $this->trans('Address', 'Admin.Global'),
                'constraints' => [
                    new TypedRegex([
                        'type' => 'address',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_ADDRESS_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressSettings::MAX_ADDRESS_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('address2', TextType::class, [
                'label' => $this->trans('Address (2)', 'Admin.Global'),
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new TypedRegex([
                        'type' => 'address',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_ADDRESS_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressSettings::MAX_ADDRESS_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('post_code', TextType::class, [
                'label' => $this->trans('Zip/Postal code', 'Admin.Global'),
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new TypedRegex([
                        'type' => 'post_code',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_POST_CODE_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressSettings::MAX_POST_CODE_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('city', TextType::class, [
                'label' => $this->trans('City', 'Admin.Global'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                    new TypedRegex([
                        'type' => 'city_name',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_CITY_NAME_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressSettings::MAX_CITY_NAME_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('id_country', CountryChoiceType::class, [
                'label' => $this->trans('Country', 'Admin.Global'),
                'attr' => [
                    'class' => 'js-manufacturer-country-select',
                    'data-states-url' => $this->router->generate('admin_country_states'),
                ],
                'required' => true,
                'with_dni_attr' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'This field cannot be empty.', 'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('id_state', ChoiceType::class, [
                'label' => $this->trans('State', 'Admin.Global'),
                'required' => true,
                'choices' => $stateChoices,
                'constraints' => [
                    new AddressStateRequired([
                        'id_country' => $countryId,
                    ]),
                ],
            ])
            ->add('dni', TextType::class, [
                'label' => $this->trans('DNI', 'Admin.Global'),
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
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => 16]
                        ),
                    ]),
                ],
            ])
            ->add('home_phone', TextType::class, [
                'label' => $this->trans('Phone', 'Admin.Global'),
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new TypedRegex([
                        'type' => 'phone_number',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_PHONE_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressSettings::MAX_PHONE_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('mobile_phone', TextType::class, [
                'label' => $this->trans('Mobile phone', 'Admin.Global'),
                'required' => false,
                'empty_data' => '',
                'constraints' => [
                    new TypedRegex([
                        'type' => 'phone_number',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_PHONE_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressSettings::MAX_PHONE_LENGTH]
                        ),
                    ]),
                ],
            ])
            ->add('other', TextType::class, [
                'label' => $this->trans('Other', 'Admin.Global'),
                'required' => false,
                'help' => $otherHint,
                'empty_data' => '',
                'constraints' => [
                    new TypedRegex([
                        'type' => 'message',
                    ]),
                    new Length([
                        'max' => AddressSettings::MAX_OTHER_LENGTH,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => AddressSettings::MAX_OTHER_LENGTH]
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
