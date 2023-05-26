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
use PrestaShop\PrestaShop\Core\MailTemplate\Layout\Layout;
use PrestaShop\PrestaShop\Core\MailTemplate\ThemeCatalogInterface;
use PrestaShopBundle\Form\Admin\Type\ColorPickerType;
use PrestaShopBundle\Form\Admin\Type\TranslatableChoiceType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Type is used to created form for order state add/edit actions
 */
class OrderStateType extends TranslatorAwareType
{
    /**
     * @var array
     */
    private $templates;

    /**
     * @var array
     */
    private $templateAttributes;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param ThemeCatalogInterface $themeCatalog
     * @param UrlGeneratorInterface $routing
     * @param ShopConfigurationInterface $configuration
     *
     * @throws \PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        ThemeCatalogInterface $themeCatalog,
        UrlGeneratorInterface $routing,
        ShopConfigurationInterface $configuration
    ) {
        parent::__construct($translator, $locales);
        $mailTheme = $configuration->get('PS_MAIL_THEME', 'modern');

        $mailLayouts = $themeCatalog->getByName($mailTheme)->getLayouts();

        foreach ($locales as $locale) {
            $languageId = $locale['id_lang'];
            $this->templates[$languageId] = $this->templateAttributes[$languageId] = [];

            /** @var Layout $mailLayout */
            foreach ($mailLayouts as $mailLayout) {
                $this->templates[$languageId][$mailLayout->getName()] = $mailLayout->getName();
                $this->templateAttributes[$languageId][$mailLayout->getName()] = [
                    'data-preview' => $routing->generate(
                        empty($mailLayout->getModuleName()) ?
                            'admin_mail_theme_preview_layout' :
                            'admin_mail_theme_preview_module_layout',
                        [
                            'theme' => $mailTheme,
                            'layout' => $mailLayout->getName(),
                            'type' => 'html',
                            'locale' => $locale['iso_code'],
                            'module' => $mailLayout->getModuleName(),
                        ]
                    ),
                ];
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TranslatableType::class, [
                'type' => TextType::class,
                'constraints' => [
                    new DefaultLanguage(),
                ],
                'options' => [
                    'constraints' => [
                        new TypedRegex([
                            'type' => 'generic_name',
                        ]),
                    ],
                ],
            ])
            ->add('color', ColorPickerType::class, [
                'required' => true,
            ])
            ->add('loggable', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Consider the associated order as validated.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
            ])
            ->add('invoice', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Allow a customer to download and view PDF versions of their invoices.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
            ])
            ->add('hidden', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Hide this status in all customer orders.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
            ])
            ->add('send_email', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Send an email to the customer when their order status has changed.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
            ])
            ->add('pdf_invoice', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Attach invoice PDF to email.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
            ])
            ->add('pdf_delivery', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Attach delivery slip PDF to email.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
            ])
            ->add('shipped', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Set the order as shipped.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
            ])
            ->add('paid', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Set the order as paid.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
            ])
            ->add('delivery', CheckboxType::class, [
                'required' => false,
                'label' => $this->trans('Set the order as in transit.', 'Admin.Shopparameters.Feature'),
                'attr' => [
                    'material_design' => true,
                ],
            ])
            ->add('template', TranslatableChoiceType::class, [
                'hint' => sprintf(
                    '%s<br>%s',
                    $this->trans('Only letters, numbers and underscores ("_") are allowed.', 'Admin.Shopparameters.Help'),
                    $this->trans('Email template for both .html and .txt.', 'Admin.Shopparameters.Help')
                ),
                'required' => false,
                'choices' => $this->templates,
                'row_attr' => $this->templateAttributes,
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
