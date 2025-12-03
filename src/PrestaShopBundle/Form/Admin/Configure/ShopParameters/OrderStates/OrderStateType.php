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
declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\OrderStates;

use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\DefaultLanguage;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\OrderState\OrderStateSettings;
use PrestaShop\PrestaShop\Core\Email\LegacyEmailTemplateLister;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\Layout;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCatalogInterface;
use PrestaShopBundle\Form\Admin\Type\ColorPickerType;
use PrestaShopBundle\Form\Admin\Type\TranslatableChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Type is used to created form for order state add/edit actions
 */
class OrderStateType extends TranslatorAwareType
{
    protected const NAME_CHARS = '!<>,;?=+()@#"{}_$%:';

    /**
     * @var array
     */
    private $templates;

    /**
     * @var array
     */
    private $templateAttributes;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ThemeCatalogInterface $themeCatalog,
        UrlGeneratorInterface $routing,
        ShopConfigurationInterface $configuration,
        LegacyEmailTemplateLister $legacyTemplateLister
    ) {
        parent::__construct($translator, $locales);

        // Load all layouts for the current mail theme
        $mailTheme = $configuration->get('PS_MAIL_THEME', 'modern');
        $mailLayouts = $themeCatalog->getByName($mailTheme)->getLayouts();

        foreach ($locales as $locale) {
            $languageId = $locale['id_lang'];
            $this->templates[$languageId] = $this->templateAttributes[$languageId] = [];

            /** @var Layout $mailLayout */
            foreach ($mailLayouts as $mailLayout) {
                $templateName = $mailLayout->getName();

                // Add all templates as a choice for order status email.
                $this->templates[$languageId][$templateName] = $templateName;

                // Add preview URL for each template
                $this->templateAttributes[$languageId][$templateName] = [
                    'data-preview' => $routing->generate(
                        empty($mailLayout->getModuleName()) ?
                            'admin_mail_theme_preview_layout' :
                            'admin_mail_theme_preview_module_layout',
                        [
                            'theme' => $mailTheme,
                            'layout' => $templateName,
                            'type' => 'html',
                            'locale' => $locale['iso_code'],
                            'module' => $mailLayout->getModuleName(),
                        ]
                    ),
                ];
            }

            // Add legacy templates that are missing in the current mail theme
            $legacyTemplates = $legacyTemplateLister->getLegacyTemplates($locale['iso_code']);
            foreach ($legacyTemplates as $templateName => $templateInfo) {
                if (!isset($this->templates[$languageId][$templateName])) {
                    $this->templates[$languageId][$templateName] = $templateName;
                    $this->templateAttributes[$languageId][$templateName] = [
                        'data-preview' => '#',
                        'data-legacy' => 'true',
                    ];
                }
            }
        }

        // Sort templates alpabetically
        foreach ($this->templates as &$templatesByLanguageId) {
            asort($templatesByLanguageId);
        }
        unset($templatesByLanguageId);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, [
                'label' => $this->trans('Status name', 'Admin.Shopparameters.Feature'),
                'help' => sprintf(
                    '%s %s %s',
                    $this->trans('Order status (e.g. \'Pending\').', 'Admin.Shopparameters.Help'),
                    $this->trans('Invalid characters: numbers and', 'Admin.Shopparameters.Help'),
                    static::NAME_CHARS
                ),
                'type' => TextType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'attr' => [
                        'autocomplete' => 'off',
                        'maxlength' => OrderStateSettings::NAME_MAX_LENGTH,
                    ],
                    'constraints' => [
                        new TypedRegex([
                            'type' => TypedRegex::TYPE_GENERIC_NAME,
                        ]),
                        new Length([
                            'max' => OrderStateSettings::NAME_MAX_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters',
                                'Admin.Notifications.Error',
                                [
                                    '%limit%' => OrderStateSettings::NAME_MAX_LENGTH,
                                ]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('icon', FileType::class, [
                'required' => false,
                'label' => $this->trans('Icon', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Image of this status used in the backoffice. (File type: .gif, suggested size: 16x16).', 'Admin.Shopparameters.Help'),
            ])
            ->add('color', ColorPickerType::class, [
                'required' => false,
                'label' => $this->trans('Color', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans('Background color of this status label. Used both in backoffice and on order tracking page. HTML colors only.', 'Admin.Shopparameters.Help'),
            ])
            ->add('loggable', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Set the associated order as validated.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
                'help' => $this->trans('This will mark the order as successfuly processed - including it in customer\'s revenue, your shop stats etc.', 'Admin.Shopparameters.Help'),
            ])
            ->add('invoice', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Generate an invoice when order is assigned this status.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
                'help' => $this->trans('If order\'s invoice is not generated yet, this will generate it.', 'Admin.Shopparameters.Help'),
            ])
            ->add('hidden', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Don\'t display this status to the customer on order tracking page.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
                'help' => $this->trans('Use this if you want this status to be internal only.', 'Admin.Shopparameters.Help'),
            ])
            ->add('shipped', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Set the associated order as shipped.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
                'help' => $this->trans('This will mark the order as shipped. It will register a stock movement entry, prevent modifying the order and other things.', 'Admin.Shopparameters.Help'),
            ])
            ->add('paid', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Set the associated order as paid.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
                'help' => $this->trans('This will add an entry to order\'s "Payments" table, if it doesn\'t exist yet.', 'Admin.Shopparameters.Help'),
            ])
            ->add('delivery', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Set the associated order as delivered.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
                'help' => $this->trans('This will create a delivery slip and mark a delivery date on order\'s invoice.', 'Admin.Shopparameters.Help'),
            ])
            ->add('send_email', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Send an email to the customer.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
                'help' => $this->trans('This will send an email to the customer after assigning this status. Make sure to select proper email template below.', 'Admin.Shopparameters.Help'),
            ])
            ->add('template', TranslatableChoiceType::class, [
                'label' => $this->trans('Template', 'Admin.Shopparameters.Feature'),
                'hint' => $this->trans('Select an email template that will be sent after setting this status.', 'Admin.Shopparameters.Help'),
                'required' => false,
                'choices' => $this->templates,
                'row_attr' => $this->templateAttributes + [
                    'class' => 'order_state_template_select',
                ],
                'button' => [
                    'label' => $this->trans('Preview', 'Admin.Actions'),
                    'icon' => 'visibility',
                    'class' => 'btn btn-primary',
                    'id' => 'order_state_template_preview',
                ],
            ])
            ->add('pdf_invoice', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Attach invoice PDF to email', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
                'help' => $this->trans('This will attach invoice PDF to the sent email, if it exists. If it doesn\'t, it will have no effect.', 'Admin.Shopparameters.Help'),
            ])
            ->add('pdf_delivery', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Attach delivery slip PDF to email.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
                'help' => $this->trans('This will attach delivery slip PDF to the sent email, if it exists. If it doesn\'t, it will have no effect.', 'Admin.Shopparameters.Help'),
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'translation_domain' => 'Admin.Shopparameters.Feature',
                'allow_extra_fields' => true,
            ])
        ;
    }
}
