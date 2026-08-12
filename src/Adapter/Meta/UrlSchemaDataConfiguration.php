<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Meta;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UrlSchemaDataConfiguration is responsible for validating, updating and retrieving data used in
 * Shop parameters -> Traffic & Seo -> Seo & Urls -> Set Shop URL form field.
 */
final class UrlSchemaDataConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array
     */
    private $rules;

    /**
     * @var LanguageInterface[]
     */
    private $languages;

    /**
     * UrlSchemaDataConfiguration constructor.
     *
     * @param Configuration $configuration
     * @param Context $shopContext
     * @param FeatureInterface $multistoreFeature
     * @param array $rules
     * @param array $languages
     */
    public function __construct(Configuration $configuration, Context $shopContext, FeatureInterface $multistoreFeature, array $rules, array $languages)
    {
        parent::__construct($configuration, $shopContext, $multistoreFeature);

        $this->rules = $rules;
        $this->languages = $languages;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $configResult = [];
        $shopConstraint = $this->getShopConstraint();

        // The languages arrive as LanguageInterface objects, so array_column() cannot reach their id:
        // it reads public properties and Lang exposes the id through getId() only. It answered an empty
        // list for every route, which made both fallbacks below produce an empty value - so a route with
        // no stored configuration, and a route still holding the pre-translation string, both rendered
        // as an empty field instead of the default.
        $languageIds = array_map(
            static function (LanguageInterface $language): int {
                return $language->getId();
            },
            $this->languages
        );

        foreach ($this->rules as $routeId => $defaultRule) {
            // Get value from configuration
            $currentValue = $this->configuration->get($this->getConfigurationKey($routeId), null, $shopConstraint);
            // A value stored before a language was added holds no entry for it, and one stored
            // before a language was removed still holds its key. Starting from the default for
            // every current language and overlaying only the keys that are still languages covers
            // both: a missing entry falls back instead of rendering empty, a stale one is dropped.
            if (is_string($currentValue)) {
                $currentValue = array_fill_keys($languageIds, $currentValue);
            }
            if (!is_array($currentValue)) {
                $currentValue = [];
            }

            $configResult[$routeId] = array_replace(
                array_fill_keys($languageIds, $defaultRule),
                array_intersect_key($currentValue, array_flip($languageIds))
            );
        }

        return $configResult;
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();

            foreach ($configuration as $routeId => $value) {
                $this->updateConfigurationValue($this->getConfigurationKey($routeId), $routeId, $configuration, $shopConstraint);
            }
        }

        return [];
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $rulesIds = array_keys($this->rules);

        $resolver = new OptionsResolver();
        $resolver->setDefined($rulesIds);
        foreach ($rulesIds as $ruleId) {
            $resolver->setAllowedTypes($ruleId, 'array');
        }

        return $resolver;
    }

    /**
     * Gets key which is used to retrieve data from configuration table.
     *
     * @param string $routeId
     *
     * @return string
     */
    private function getConfigurationKey($routeId)
    {
        return sprintf('PS_ROUTE_%s', $routeId);
    }
}
