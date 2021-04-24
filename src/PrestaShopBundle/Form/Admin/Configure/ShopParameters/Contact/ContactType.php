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

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\NoTags;
use PrestaShopBundle\Form\Admin\Type\EmailType;
use PrestaShopBundle\Form\Admin\Type\ShopChoiceTreeType;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

/**
 * Class ContactType
 */
class ContactType extends TranslatorAwareType
{
    public const MAX_TITLE_LENGTH = 255;

    /**
     * @var bool
     */
    private $isShopFeatureEnabled;

    /**
     * @var DataTransformerInterface
     */
    private $singleDefaultLanguageArrayToFilledArrayDataTransformer;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param DataTransformerInterface $singleDefaultLanguageArrayToFilledArrayDataTransformer
     * @param bool $isShopFeatureEnabled
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        DataTransformerInterface $singleDefaultLanguageArrayToFilledArrayDataTransformer,
        $isShopFeatureEnabled
    ) {
        parent::__construct($translator, $locales);
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
                'label' => $this->trans('Title', 'Admin.Global'),
                'help' => $this->trans('Contact name (e.g. Customer Support).', 'Admin.Shopparameters.Help'),
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new Regex([
                            'pattern' => '/^[^<>={}]*$/u',
                            'message' => $this->trans(
                                '%s is invalid.',
                                'Admin.Notifications.Error'
                            ),
                        ]
                        ),
                        new Length([
                            'max' => static::MAX_TITLE_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                ['%limit%' => static::MAX_TITLE_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => $this->trans('Email address', 'Admin.Global'),
                'help' => $this->trans('Emails will be sent to this address.', 'Admin.Shopparameters.Help'),
                'required' => false,
                'constraints' => [
                    new Email([
                        'message' => $this->trans(
                            '%s is invalid.',
                            'Admin.Notifications.Error'
                        ),
                    ]),
                ],
            ])
            ->add('is_messages_saving_enabled', SwitchType::class, [
                'label' => $this->trans('Save messages?', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('If enabled, all messages will be saved in the "Customer Service" page under the "Customer" menu.', 'Admin.Shopparameters.Help'),
            ])
            ->add('description', TranslatableType::class, [
                'label' => $this->trans('Description', 'Admin.Global'),
                'type' => TextareaType::class,
                'required' => false,
                'options' => [
                    'constraints' => [
                        new NoTags([
                            'message' => $this->trans(
                                'The "%s" field is invalid. HTML tags are not allowed.',
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
                'label' => $this->trans('Shop association', 'Admin.Global'),
                'constraints' => [
                    new NotBlank([
                        'message' => $this->trans(
                            'The %s field is required.',
                            'Admin.Notifications.Error',
                            [
                                sprintf('"%s"', $this->trans('Shop association', 'Admin.Global')),
                            ]
                        ),
                    ]),
                ],
            ]);
        }
    }
}
