<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
