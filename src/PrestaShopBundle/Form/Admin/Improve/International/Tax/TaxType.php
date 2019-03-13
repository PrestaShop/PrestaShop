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

namespace PrestaShopBundle\Form\Admin\Improve\International\Tax;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Form type for tax add/edit
 */
class TaxType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

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
            ->add('name', TranslatableType::class, [
                'options' => [
                    'constraints' => [
                        new DefaultLanguage(),
                        new Length([
                            'max' => 32,
                            'maxMessage' => $this->translator->trans(
                                'This field cannot be longer than %limit% characters',
                                ['%limit%' => 32],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                        new Regex([
                            //@todo: isGenericName Constraint istead
                            'pattern' => '/^[^<>={}]*$/u',
                            'message' => $this->translator->trans(
                                '%s is invalid.',
                                [
                                    sprintf('"%s"', $this->translator->trans('Name', [], 'Admin.Global')),
                                ],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('rate', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans(
                            'The %s field is required.',
                            [
                                sprintf('"%s"', $this->translator->trans(
                                    'Rate', [], 'Admin.International.Feature'
                                )),
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new Length([
                        'max' => 6,
                        'maxMessage' => $this->translator->trans(
                            'This field cannot be longer than %limit% characters',
                            ['%limit%' => 6],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                    new Type([
                        'type' => 'numeric',
                        'message' => $this->translator->trans(
                            'This field is invalid, it must contain numeric values',
                            [],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('is_enabled', SwitchType::class, [
                'required' => false,
            ])
        ;
    }
}
