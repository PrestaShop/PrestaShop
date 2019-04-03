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

namespace PrestaShopBundle\Form\Admin\Sell\Address;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegexConstraint;
use PrestaShop\PrestaShop\Core\Form\ConfigurableFormChoiceProviderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
     */
    public function __construct(
        array $manufacturerChoices,
        array $countryChoices,
        ConfigurableFormChoiceProviderInterface $statesChoiceProvider,
        $contextCountryId,
        TranslatorInterface $translator
    ) {
        $this->manufacturerChoices = $manufacturerChoices;
        $this->countryChoices = $countryChoices;
        $this->statesChoiceProvider = $statesChoiceProvider;
        $this->contextCountryId = $contextCountryId;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['country_id'] !== 0) {
            $countryIdForStateChoices = $options['country_id'];
        } else {
            $countryIdForStateChoices = $this->contextCountryId;
        }

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
                    new TypedRegexConstraint([
                        'type' => 'name',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => 255],
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
                    new TypedRegexConstraint([
                        'type' => 'name',
                    ]),
                    new Length([
                        'max' => 255,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => 255],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('address', TextType::class, [
                'constraints' => [
                    new TypedRegexConstraint([
                        'type' => 'address',
                    ]),
                    new Length([
                        'max' => 128,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => 128],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('address2', TextType::class, [
                'required' => false,
                'constraints' => [
                    new TypedRegexConstraint([
                        'type' => 'address',
                    ]),
                    new Length([
                        'max' => 128,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => 128],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('post_code', TextType::class, [
                'required' => false,
                'constraints' => [
                    new TypedRegexConstraint([
                        'type' => 'post_code',
                    ]),
                    new Length([
                        'max' => 12,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => 12],
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
                    new TypedRegexConstraint([
                        'type' => 'city_name',
                    ]),
                    new Length([
                        'max' => 64,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => 64],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('id_country', ChoiceType::class, [
                'required' => true,
                'choices' => $this->countryChoices,
                'translation_domain' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty', [], 'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('id_state', ChoiceType::class, [
                'required' => false,
                'translation_domain' => false,
                'choices' => $this->statesChoiceProvider->getChoices([
                    'id_country' => $countryIdForStateChoices,
                ]),
            ])
            ->add('home_phone', TextType::class, [
                'required' => false,
                'constraints' => [
                    new TypedRegexConstraint([
                        'type' => 'phone_number',
                    ]),
                    new Length([
                        'max' => 32,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => 32],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('mobile_phone', TextType::class, [
                'required' => false,
                'constraints' => [
                    new TypedRegexConstraint([
                        'type' => 'phone_number',
                    ]),
                    new Length([
                        'max' => 32,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => 32],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('other', TextType::class, [
                'required' => false,
                'constraints' => [
                    new TypedRegexConstraint([
                        'type' => 'message',
                    ]),
                    new Length([
                        'max' => 300,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => 300],
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

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'country_id' => 0,
            ])
            ->setAllowedTypes('country_id', 'integer')
        ;
    }
}
