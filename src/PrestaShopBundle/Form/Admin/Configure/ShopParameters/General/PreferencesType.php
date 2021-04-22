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
use PrestaShopBundle\Form\Admin\Type\SwitchType;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class returning the content of the form in the maintenance page.
 * To be found in Configure > Shop parameters > General > Maintenance.
 */
class PreferencesType extends TranslatorAwareType
{
    /**
     * @var bool
     */
    private $isMultistoreUsed;

    /**
     * @var bool
     */
    private $isSingleShopContext;

    /**
     * @var bool
     */
    private $isAllShopContext;

    /**
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param bool $isMultistoreUsed
     * @param bool $isSingleShopContext
     * @param bool $isAllShopContext
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        $isMultistoreUsed,
        $isSingleShopContext,
        $isAllShopContext
    ) {
        parent::__construct($translator, $locales);

        $this->isMultistoreUsed = $isMultistoreUsed;
        $this->isSingleShopContext = $isSingleShopContext;
        $this->isAllShopContext = $isAllShopContext;
    }

    /**
     * @var bool
     */
    private $isSecure;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $configuration = $this->getConfiguration();
        $isSslEnabled = $configuration->getBoolean('PS_SSL_ENABLED');

        if ($this->isSecure) {
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
                    'The multistore feature allows you to manage several e-shops with one Back Office. If this feature is enabled, a "Multistore" page will be available in the "Advanced Parameters" menu.',
                    'Admin.Shopparameters.Help'
                ),
            ])
            ->add('shop_activity', ChoiceType::class, [
                'required' => false,
                'placeholder' => $this->trans('-- Please choose your main activity --', 'Install'),
                'choices' => [
                    'Animals and Pets' => 2,
                    'Art and Culture' => 3,
                    'Babies' => 4,
                    'Beauty and Personal Care' => 5,
                    'Cars' => 6,
                    'Computer Hardware and Software' => 7,
                    'Download' => 8,
                    'Fashion and accessories' => 9,
                    'Flowers, Gifts and Crafts' => 10,
                    'Food and beverage' => 11,
                    'HiFi, Photo and Video' => 12,
                    'Home and Garden' => 13,
                    'Home Appliances' => 14,
                    'Jewelry' => 15,
                    'Lingerie and Adult' => 1,
                    'Mobile and Telecom' => 16,
                    'Services' => 17,
                    'Shoes and accessories' => 18,
                    'Sport and Entertainment' => 19,
                    'Travel' => 20,
                ],
                'label' => $this->trans('Main Shop Activity', 'Admin.Shopparameters.Feature'),
                'choice_translation_domain' => 'Install',
            ]);
    }

    /**
     * Enabled only if the form is accessed using HTTPS protocol.
     *
     * @var bool
     */
    public function setIsSecure($isSecure)
    {
        $this->isSecure = $isSecure;
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
        if (!$this->isMultistoreUsed && $this->isSingleShopContext) {
            return true;
        }

        return $this->isAllShopContext;
    }
}
