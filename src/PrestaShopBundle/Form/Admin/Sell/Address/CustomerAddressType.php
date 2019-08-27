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

use PrestaShopBundle\Form\Admin\Type\CountryChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form type for address add/edit
 */
class CustomerAddressType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $currencyChoices;

    /**
     * @param TranslatorInterface $translator
     * @param array $currencyChoices
     */
    public function __construct(
        TranslatorInterface $translator,
        array $currencyChoices
    ) {
        $this->translator = $translator;
        $this->currencyChoices = $currencyChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $builder->getData();

        if (!isset($data['id_customer'])) {
            $builder->add('customer_email', EmailType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
        }

        if ($this->isRequired('phone_mobile')) {
            $builder->add('phone_mobile', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ]);
        }

        $builder
            //TODO check if required by country
            ->add('dni', TextType::class, [
                'required' => $this->isRequired('dni'),
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => 'dni',
                        ]
                    ),
                ],
            ])
            ->add('alias', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('first_name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('last_name', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('company', TextType::class, [
                'required' => $this->isRequired('company'),
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => 'company',
                        ]
                    ),
                ],
            ])
            ->add('vat_number', TextType::class, [
                'required' => $this->isRequired('vat_number'),
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => 'vat_number',
                        ]
                    ),
                ],
            ])
            ->add('address1', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('address2', TextType::class, [
                'required' => $this->isRequired('address2'),
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => 'address2',
                        ]
                    ),
                ],
            ])
            ->add('city', TextType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            //TODO check if needed by country and country zip code format
            ->add('postcode', TextType::class, [
                'required' => $this->isRequired('postcode'),
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => 'postcode',
                        ]
                    ),
                ],
            ])
            ->add('id_country', CountryChoiceType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            //TODO if country has sates, state has to be selected
            //TODO Add check for initial display none
            ->add('id_state', CountryChoiceType::class, [
                'required' => false,
                'disabled' => true,
            ])
            ->add('phone', TextType::class, [
                'required' => $this->isRequired('phone'),
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => 'phone',
                        ]
                    ),
                ],
            ])
            ->add('other', TextareaType::class, [
                'required' => $this->isRequired('other'),
                'constraints' => [
                    new NotBlank(
                        [
                            'groups' => 'other',
                        ]
                    ),
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'validation_groups' => function () {
                    $groups = ['Default'];

                    //TODO add required fields as validation groups

                    return $groups;
                },
            ]
        );
    }

    /**
     * @param string $field
     *
     * @return bool
     */
    private function isRequired(string $field): bool
    {
        //TODO implement required fields check
        return true;
    }
}
