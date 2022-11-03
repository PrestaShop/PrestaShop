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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Improve\International\Tax;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\ConstraintValidator\TypedRegexValidator;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Backwards compatibility break introduced in 1.7.8.0 due to extension of TranslatorAwareType
 * Form type for tax add/edit
 */
class TaxType extends TranslatorAwareType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $invalidCharsText = sprintf(
            '%s ' . TypedRegexValidator::GENERIC_NAME_CHARS,
            $this->trans('Invalid characters:', 'Admin.Notifications.Info')
        );

        $nameHintText =
            $this->trans(
                'Tax name to display in carts and on invoices (e.g. "VAT").',
                'Admin.International.Help'
            )
            . PHP_EOL
            . $invalidCharsText;

        $builder
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Name', 'Admin.Global'),
                'help' => $nameHintText,
                'options' => [
                    'constraints' => [
                        new Length([
                            'max' => 32,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => 32]
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
            ->add('rate', TextType::class, [
                'label' => $this->trans('Rate', 'Admin.International.Feature'),
                'help' => $this->trans(
                    'Format: XX.XX or XX.XXX (e.g. 19.60 or 13.925)',
                    'Admin.International.Help'
                ),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [
                                sprintf('"%s"', $this->trans(
                                    'Rate', 'Admin.International.Feature'
                                )),
                            ]
                        ),
                    ]),
                    new Length([
                        'max' => 6,
                        'maxMessage' => $this->trans(
                            'This field cannot be longer than %limit% characters',
                            'Admin.Notifications.Error',
                            ['%limit%' => 6]
                        ),
                    ]),
                    new Type([
                        'type' => 'numeric',
                        'message' => $this->trans(
                            'This field is invalid, it must contain numeric values',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('is_enabled', SwitchType::class, [
                'label' => $this->trans('Enable', 'Admin.Actions'),
                'required' => false,
            ])
        ;
    }
}
