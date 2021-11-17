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

namespace PrestaShopBundle\Form\Admin\Sell\Product\SEO;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\TypedRegex;
use PrestaShop\PrestaShop\Core\Domain\Product\ProductSettings;
use PrestaShopBundle\Form\Admin\Type\TextWithLengthCounterType;
use PrestaShopBundle\Form\Admin\Type\TranslatableType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Length;

class SEOType extends TranslatorAwareType
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var bool
     */
    private $friendlyUrlEnabled;

    /**
     * @var bool
     */
    private $forceFriendlyUrl;

    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param RouterInterface $router
     * @param bool $friendlyUrlEnabled
     * @param bool $forceFriendlyUrl
     * @param LegacyContext $legacyContext
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        RouterInterface $router,
        bool $friendlyUrlEnabled,
        bool $forceFriendlyUrl,
        LegacyContext $legacyContext
    ) {
        parent::__construct($translator, $locales);
        $this->router = $router;
        $this->friendlyUrlEnabled = $friendlyUrlEnabled;
        $this->forceFriendlyUrl = $forceFriendlyUrl;
        $this->legacyContext = $legacyContext;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('serp', SerpType::class)
            ->add('meta_title', TranslatableType::class, [
                'label' => $this->trans('Meta title', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('Public title for the product page, and for search engines. Leave blank to use the product name. The number of remaining characters is displayed to the right of the field.', 'Admin.Catalog.Help'),
                'required' => false,
                'type' => TextWithLengthCounterType::class,
                'help' => $this->trans(
                    'Public title for the product page, and for search engines. Leave blank to use the product name. The number of remaining characters is displayed to the right of the field.',
                    'Admin.Catalog.Help'
                ),
                'options' => [
                    'input' => 'text',
                    'input_attr' => [
                        'class' => 'serp-watched-title',
                    ],
                    'max_length' => ProductSettings::MAX_META_TITLE_LENGTH,
                    'position' => 'after',
                    'constraints' => [
                        new Length([
                            'max' => ProductSettings::MAX_META_TITLE_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                ['%limit%' => ProductSettings::MAX_META_TITLE_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('meta_description', TranslatableType::class, [
                'label' => $this->trans('Meta description', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('This description will appear in search engines. You need a single sentence, shorter than 160 characters (including spaces)', 'Admin.Catalog.Help'),
                'required' => false,
                'type' => TextWithLengthCounterType::class,
                'help' => $this->trans(
                    'This description will appear in search engines. It should be a single sentence, shorter than 160 characters (including spaces).',
                    'Admin.Catalog.Help'
                ),
                'options' => [
                    'input' => 'textarea',
                    'input_attr' => [
                        'class' => 'serp-watched-description',
                    ],
                    'max_length' => ProductSettings::MAX_META_DESCRIPTION_LENGTH,
                    'position' => 'after',
                    'constraints' => [
                        new Length([
                            'max' => ProductSettings::MAX_META_DESCRIPTION_LENGTH,
                            'maxMessage' => $this->trans(
                                'This field cannot be longer than %limit% characters.',
                                'Admin.Notifications.Error',
                                ['%limit%' => ProductSettings::MAX_META_DESCRIPTION_LENGTH]
                            ),
                        ]),
                    ],
                ],
            ])
            ->add('link_rewrite', TranslatableType::class, [
                'label' => $this->trans('Friendly URL', 'Admin.Catalog.Feature'),
                'label_help_box' => $this->trans('This is the human-readable URL, as generated from the product\'s name. You can change it if you want.', 'Admin.Catalog.Help'),
                'required' => false,
                'type' => TextType::class,
                'help' => $this->trans(
                    'This is the human-readable URL, as generated from the product\'s name. You can change it if you want.',
                    'Admin.Catalog.Help'
                ),
                'alert_message' => $this->getFriendlyAlterMessages(),
                'options' => [
                    'attr' => [
                        'class' => 'serp-watched-url',
                    ],
                ],
            ])
            ->add('redirect_option', RedirectOptionType::class)
            ->add('tags', TranslatableType::class, [
                'required' => false,
                'label' => $this->trans('Tags', 'Admin.Catalog.Feature'),
                'label_tag_name' => 'h2',
                'help' => $this->trans('Use a comma to create separate tags. E.g.: dress, cotton, party dresses.', 'Admin.Catalog.Help'),
                'options' => [
                    'constraints' => [
                        new TypedRegex(TypedRegex::TYPE_GENERIC_NAME),
                    ],
                    'attr' => [
                        'class' => 'js-taggable-field',
                    ],
                    'required' => false,
                ],
                'alert_title' => $this->trans('Tags are meant to help your customers find your products via the search bar.', 'Admin.Catalog.Help'),
                'alert_message' => [
                    $this->trans('Choose terms and keywords that your customers will use to search for this product and make sure you are consistent with the tags you may have already used.', 'Admin.Catalog.Help'),
                    $this->trans('You can manage tag aliases in the [1]Search section[/1]. If you add new tags, you have to rebuild the index.', 'Admin.Catalog.Help', [
                        '[1]' => sprintf(
                            '<a target="_blank" href="%s">',
                            $this->legacyContext->getAdminLink('AdminSearchConf')
                        ),
                        '[/1]' => '</a>',
                    ]),
                ],
            ])
        ;
    }

    /**
     * @return string[]
     */
    private function getFriendlyAlterMessages(): array
    {
        $alertMessages = [];
        $friendlyUrl = $this->router->generate('admin_metas_index') . '#meta_settings_set_up_urls_form';
        $productPreferencesUrl = $this->router->generate('admin_product_preferences') . '#configuration_fieldset_products';

        if ($this->friendlyUrlEnabled) {
            $alertMessages[] = sprintf(
                '<strong>%s</strong> %s',
                $this->trans('Friendly URLs are currently enabled.', 'Admin.Catalog.Notification'),
                $this->trans('To disable it, go to [1]SEO and URLs[/1]', 'Admin.Catalog.Notification', [
                    '[1]' => '<a target="_blank" href="' . $friendlyUrl . '">',
                    '[/1]' => '</a>',
                ])
            );
        } else {
            $alertMessages[] = sprintf(
                '<strong>%s</strong> %s',
                $this->trans('Friendly URLs are currently disabled.', 'Admin.Catalog.Notification'),
                $this->trans('To enable it, go to [1]SEO and URLs[/1]', 'Admin.Catalog.Notification', [
                    '[1]' => '<a target="_blank" href="' . $friendlyUrl . '">',
                    '[/1]' => '</a>',
                ])
            );
        }
        if ($this->forceFriendlyUrl) {
            $alertMessages[] = sprintf(
                '<strong>%s</strong> %s',
                $this->trans('The "Force update of friendly URL" option is currently enabled.', 'Admin.Catalog.Notification'),
                $this->trans('To disable it, go to [1]Product Settings[/1]', 'Admin.Catalog.Notification', [
                    '[1]' => '<a target="_blank" href="' . $productPreferencesUrl . '">',
                    '[/1]' => '</a>',
                ])
            );
        }

        return $alertMessages;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'label' => $this->trans('Search Engine Optimization', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h2',
            'label_subtitle' => $this->trans('Improve your ranking and how your product page will appear in search engines results.', 'Admin.Catalog.Feature'),
            'required' => false,
        ]);
    }
}
