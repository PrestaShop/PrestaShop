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

namespace PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This form type is needed to display custom list of Catalog Price Rules.
 * Check src/PrestaShopBundle/Resources/views/Admin/Sell/Catalog/Product/FormTheme/catalog_price_rules.html.twig
 * for implementation.
 */
class CatalogPriceRulesType extends TranslatorAwareType
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * PricingType constructor.
     *
     * @param TranslatorInterface $translator
     * @param array $locales
     * @param LegacyContext $legacyContext
     */
    public function __construct(
        TranslatorInterface $translator,
        array $locales,
        LegacyContext $legacyContext
    ) {
        parent::__construct($translator, $locales);
        $this->legacyContext = $legacyContext;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        /**
         * %catalog_price_rule_id% can't be used in this function, because getAdminLink adds unneeded stuff to % while creating url
         * That's why catalog_price_rule_id is used and then string replaced.
         */
        $catalogPriceRuleEditLink = $this->legacyContext->getAdminLink(
            'AdminSpecificPriceRule',
            true,
            ['updatespecific_price_rule' => '', 'id_specific_price_rule' => 'catalog_price_rule_id']
        );
        $catalogPriceRuleIndexLink = $this->legacyContext->getAdminLink('AdminSpecificPriceRule');
        /** Adding % to make link more unique */
        $catalogPriceRuleEditLink = str_replace('catalog_price_rule_id', '%catalog_price_rule_id%', $catalogPriceRuleEditLink);

        $resolver->setDefaults([
            'form_theme' => '@PrestaShop/Admin/Sell/Catalog/Product/FormTheme/catalog_price_rules.html.twig',
            'label' => $this->trans('Catalog price rules', 'Admin.Catalog.Feature'),
            'label_tag_name' => 'h2',
            'attr' => [
                'data-catalog-price-url' => $catalogPriceRuleEditLink,
            ],
            'external_link' => [
                'text' => $this->trans('[1]Manage catalog price rules[/1]', 'Admin.Catalog.Feature'),
                'href' => $catalogPriceRuleIndexLink,
                'align' => 'left',
            ],
            'row_attr' => [
                'id' => 'catalog-price-rules-container',
                'class' => 'd-none',
            ],
        ]);
    }
}
