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
        $isSslEnabled = (bool) $configuration->get('PS_SSL_ENABLED');

        if ($this->requestStack->getCurrentRequest()->isSecure()) {
            $builder->add('enable_ssl', SwitchType::class, [
                'label' => $this->trans('Enable SSL', 'Admin.Shopparameters.Feature'),
                'help' => $this->trans(
                    'If you own an SSL certificate for your shop\'s domain name, you can activate SSL encryption (https://) for customer account identification and order processing.',
                    'Admin.Shopparameters.Help'
                ),
            ]);
        }

        $builder
            ->add('enable_ssl_everywhere', SwitchType::class, [
                'disabled' => !$isSslEnabled,
                'label' => $this->trans(
                    'Enable SSL on all pages',
                    'Admin.Shopparameters.Feature'
                ),
                'help' => $this->trans(
                    'When enabled, all the pages of your shop will be SSL-secured.',
                    'Admin.Shopparameters.Help'
                ),
            ])
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
                    'Allow iframes on text fields like product description. We recommend that you leave this option disabled.',
                    'Admin.Shopparameters.Help'
                ),
            ])
            ->add('use_htmlpurifier', SwitchType::class, [
                'label' => $this->trans(
                        'Use HTMLPurifier Library',
                        'Admin.Shopparameters.Feature'
                    ),
                'help' => $this->trans(
                    'Clean the HTML content on text fields. We recommend that you leave this option enabled.',
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
                        'Enable suppliers page on your front office even when its module is disabled.',
                        'Admin.Shopparameters.Help'
                    ),
                ])
            ->add(
                'display_manufacturers', SwitchType::class, [
                    'label' => $this->trans('Display brands', 'Admin.Shopparameters.Feature'),
                    'help' => $this->trans(
                        'Enable brands page on your front office even when its module is disabled.',
                        'Admin.Shopparameters.Help'
                    ),
                ])
            ->add(
                'display_best_sellers', SwitchType::class, [
                    'label' => $this->trans('Display best sellers', 'Admin.Shopparameters.Feature'),
                    'help' => $this->trans(
                        'Enable best sellers page on your front office even when its respective module is disabled.',
                        'Admin.Shopparameters.Help'
                    ),
                ])
            ->add('multishop_feature_active', SwitchType::class, [
                'disabled' => !$this->isContextDependantOptionEnabled(),
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
