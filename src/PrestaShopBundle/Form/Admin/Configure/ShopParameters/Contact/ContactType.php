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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\Contact;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Translation\TranslatorAwareTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class ContactType
 */
class ContactType extends AbstractType
{
    use TranslatorAwareTrait;

    /**
     * @var bool
     */
    private $isShopFeatureEnabled;

    /**
     * @var DataTransformerInterface
     */
    private $singleDefaultLanguageArrayToFilledArrayDataTransformer;

    /**
     * @param DataTransformerInterface $singleDefaultLanguageArrayToFilledArrayDataTransformer
     * @param bool $isShopFeatureEnabled
     */
    public function __construct(
        DataTransformerInterface $singleDefaultLanguageArrayToFilledArrayDataTransformer,
        $isShopFeatureEnabled
    ) {
        $this->isShopFeatureEnabled = $isShopFeatureEnabled;
        $this->singleDefaultLanguageArrayToFilledArrayDataTransformer = $singleDefaultLanguageArrayToFilledArrayDataTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TranslatableType::class, [
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new Regex([
                                'pattern' => '/^[^<>={}]*$/u',
                                'message' => $this->trans(
                                    '%s is invalid.',
                                    [],
                                    'Admin.Notifications.Error'
                                ),
                            ]
                        ),
                    ],
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'constraints' => [
                    new Email([
                        'message' => $this->trans(
                            '%s is invalid.',
                            [],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('is_messages_saving_enabled', SwitchType::class)
            ->add('description', TranslatableType::class, [
                'type' => TextareaType::class,
                'required' => false,
                'options' => [
                    'constraints' => [
                        new CleanHtml([
                            'message' => $this->trans(
                                '%s is invalid.',
                                [],
                                'Admin.Notifications.Error'
                            ),
                        ]),
                    ],
                ],
            ])
        ;

        $builder->get('title')->addModelTransformer($this->singleDefaultLanguageArrayToFilledArrayDataTransformer);

        if ($this->isShopFeatureEnabled) {
            $builder->add('shop_association', ShopChoiceTreeType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            [
                                sprintf('"%s"', $this->trans('Shop association', [], 'Admin.Global')),
                            ],
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ]);
        }
    }
}
