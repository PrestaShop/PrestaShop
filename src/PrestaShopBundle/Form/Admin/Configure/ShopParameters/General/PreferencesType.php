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

namespace PrestaShopBundle\Form\Admin\Configure\ShopParameters\General;

use PrestaShop\PrestaShop\Adapter\Entity\Order;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class returning the content of the form in the maintenance page.
 * To be found in Configure > Shop parameters > General > Maintenance.
 */
class PreferencesType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $isShopFeatureEnabled;

    /**
     * @var bool
     */
    private $isSingleShopContext;

    /**
     * @var bool
     */
    private $isAllShopContext;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param ConfigurationInterface $configuration
     * @param bool $isShopFeatureEnabled
     * @param bool $isSingleShopContext
     * @param bool $isAllShopContext
     */
    public function __construct(
        RequestStack $requestStack,
        TranslatorInterface $translator,
        array $locales,
        ConfigurationInterface $configuration,
        bool $isShopFeatureEnabled,
        bool $isSingleShopContext,
        bool $isAllShopContext
    ) {
        parent::__construct($translator, $locales);

        $this->isShopFeatureEnabled = $isShopFeatureEnabled;
        $this->isSingleShopContext = $isSingleShopContext;
        $this->isAllShopContext = $isAllShopContext;
        $this->configuration = $configuration;
        $this->requestStack = $requestStack;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $configuration = $this->configuration;

        if ($this->requestStack->getCurrentRequest()->isSecure()) {
            $builder->add('enable_ssl', SwitchType::class, [
                'label' => $this->trans('Enable SSL', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'Enables or disables SSL encryption (https://) for your shop. This is a security standard and you should always keep this option enabled, unless there is a specific technical issue.',
                    'Admin.Shopparameters.Help'
                ),
            ]);
        }

        $builder
            ->add('enable_token', SwitchType::class, [
                'disabled' => !$this->isContextDependantOptionEnabled(),
                'label' => $this->trans(
                    'Increase front office security',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'Enable or disable token in the Front Office to improve PrestaShop\'s security.',
                    'Admin.Shopparameters.Help'
                ),
            ])
            ->add('allow_html_iframes', SwitchType::class, [
                'label' => $this->trans(
                    'Allow iframes on HTML fields',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'Allows the use of iframes in rich text fields such as product descriptions. It is recommended to keep this option disabled unless you specifically need iframes for video embeds or other external content.',
                    'Admin.Shopparameters.Help'
                ),
            ])
            ->add('use_htmlpurifier', SwitchType::class, [
                'label' => $this->trans(
                    'Use HTMLPurifier Library',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'Cleans the HTML content in rich text fields before saving. It is recommended to keep this option enabled to ensure safe and valid content is saved into the database.',
                    'Admin.Shopparameters.Help'
                ),
            ])
            ->add(
                'price_round_mode', ChoiceType::class, [
                    'placeholder' => false,
                    'choices' => [
                        'Round up away from zero, when it is half way there (recommended)' => $configuration->get('PS_ROUND_HALF_UP'),
                        'Round down towards zero, when it is half way there' => $configuration->get('PS_ROUND_HALF_DOWN'),
                        'Round towards the next even value' => $configuration->get('PS_ROUND_HALF_EVEN'),
                        'Round towards the next odd value' => $configuration->get('PS_ROUND_HALF_ODD'),
                        'Round up to the nearest value' => $configuration->get('PS_ROUND_UP'),
                        'Round down to the nearest value' => $configuration->get('PS_ROUND_DOWN'),
                    ],
                    'label' => $this->trans('Round mode', 'Admin.Shopparameters.Feature'),
                    'help' => $this->trans(
                        'You can choose among 6 different ways of rounding prices. "Round up away from zero ..." is the recommended behavior.',
                        'Admin.Shopparameters.Help'
                    ),
                ])
            ->add('price_round_type', ChoiceType::class, [
                'placeholder' => false,
                'choices' => [
                    'Round on each item' => Order::ROUND_ITEM,
                    'Round on each line' => Order::ROUND_LINE,
                    'Round on the total' => Order::ROUND_TOTAL,
                ],
                'label' => $this->trans('Round type', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'You can choose when to round prices: either on each item, each line or the total (of an invoice, for example).',
                    'Admin.Shopparameters.Help'
                ),
            ])
            ->add(
                'display_suppliers', SwitchType::class, [
                    'label' => $this->trans('Display suppliers', 'Admin.Shopparameters.Feature'),
                    'help' => $this->trans(
                        'Enables or disables the suppliers listing in your shop. When disabled, suppliers are also removed from the sitemap and other related sections.',
                        'Admin.Shopparameters.Help'
                    ),
                ])
            ->add(
                'display_manufacturers', SwitchType::class, [
                    'label' => $this->trans('Display brands', 'Admin.Shopparameters.Feature'),
                    'help' => $this->trans(
                        'Enables or disables the brands listing in your shop. When disabled, brands are also removed from the sitemap and other related sections.',
                        'Admin.Shopparameters.Help'
                    ),
                ])
            ->add(
                'display_best_sellers', SwitchType::class, [
                    'label' => $this->trans('Display best sellers', 'Admin.Shopparameters.Feature'),
                    'help' => $this->trans(
                        'Enables or disables the best sellers page in your shop. When disabled, the page and related links are also removed from the sitemap and other sections.',
                        'Admin.Shopparameters.Help'
                    ),
                ])
            ->add('multishop_feature_active', SwitchType::class, [
                // Disable the checkbox if multistore feature is active and at least 2 shops exist (@see PrestaShop/PrestaShop/Adapter/Feature/MultistoreFeature)
                'disabled' => $this->isShopFeatureEnabled,
                'label' => $this->trans('Enable Multistore', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'The multistore feature allows you to manage several front offices from a single back office. If this feature is enabled, a Multistore page is available in the Advanced Parameters menu.',
                    'Admin.Shopparameters.Help'
                ),
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'translation_domain' => 'Admin.Shopparameters.Feature',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'preferences';
    }

    /**
     * Check if option which depends on multistore context can be changed.
     *
     * @return bool
     */
    protected function isContextDependantOptionEnabled()
    {
        if (!$this->isShopFeatureEnabled && $this->isSingleShopContext) {
            return true;
        }

        return $this->isAllShopContext;
    }
}
