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

namespace PrestaShopBundle\Form\Admin\Improve\Shipping\Carrier;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Defines form part for add/edit carrier general-settings step
 */
class StepGeneralType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'This field cannot be empty',
                            [],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new TypedRegex([
                        'type' => 'carrier_name',
                        'message' => $this->translator->trans('%s is invalid.', [], 'Admin.Notifications.Error'),
                    ]),
                    new Length([
                        //@todo: const from CarrierName
                        'max' => 64,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            [
                                '%limit%' => 64,
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('transit_time', TranslatableType::class, [
                'constraints' => [
                    new DefaultLanguage([
                        'message' => $this->translator->trans(
                            'This field is required at least in your default language.',
                            [],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
                            'message' => $this->translator->trans('%s is invalid.', [], 'Admin.Notifications.Error'),
                        ]),
                    ],
                ],
            ])
            ->add('speed_grade', NumberType::class, [
                'required' => false,
                'constraints' => [
                    new LessThanOrEqual([
                        'value' => 9,
                        'message' => $this->translator->trans(
                            'Value cannot be greater than %value%.',
                            ['%value%' => 9],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 0,
                        'message' => $this->translator->trans(
                            'Value cannot be less than %value%.',
                            ['%value%' => 0],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('logo', FileType::class, [
                'required' => false,
            ])
            ->add('logo_tmp_name', HiddenType::class)
            ->add('tracking_url', TextType::class, [
                'required' => false,
                'constraints' => [
                    new TypedRegex([
                        'type' => 'absolute_url',
                        'message' => $this->translator->trans('%s is invalid.', [], 'Admin.Notifications.Error'),
                    ]),
                ],
            ]);
    }
}
