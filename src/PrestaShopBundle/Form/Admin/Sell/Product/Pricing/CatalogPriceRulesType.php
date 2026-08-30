<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Form\Admin\Sell\Product\Pricing;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagStateCheckerInterface;
use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

    private const CATALOG_PRICE_RULE_ID_PLACEHOLDER = 987654321;

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
        LegacyContext $legacyContext,
        private readonly FeatureFlagStateCheckerInterface $featureFlagStateChecker,
        private readonly UrlGeneratorInterface $urlGenerator,
    ) {
        parent::__construct($translator, $locales);
        $this->legacyContext = $legacyContext;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        // When the Catalog price rules feature is enabled, links must point directly
        // to the new Symfony pages. Using getAdminLink() would resolve the legacy
        // controller to the Symfony route and attempt to generate it with a textual
        // placeholder that does not satisfy the route's numeric requirement.
        if ($this->featureFlagStateChecker->isEnabled('catalog_price_rule')) {
            $catalogPriceRuleIndexLink = $this->urlGenerator->generate(
                'admin_catalog_price_rules_index'
            );

            // The edit route only accepts a numeric catalogPriceRuleId, so the final
            // JavaScript placeholder cannot be passed directly to the URL generator.
            // A temporary numeric value is used to generate a valid URL and is then
            // replaced with the placeholder expected by the JavaScript component.
            $catalogPriceRuleEditLink = $this->urlGenerator->generate(
                'admin_catalog_price_rules_edit',
                [
                    'catalogPriceRuleId' => self::CATALOG_PRICE_RULE_ID_PLACEHOLDER,
                ]
            );

            $catalogPriceRuleEditLink = str_replace(
                sprintf('/%d/edit', self::CATALOG_PRICE_RULE_ID_PLACEHOLDER),
                '/%catalog_price_rule_id%/edit',
                $catalogPriceRuleEditLink
            );
        } else {
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
        }

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
