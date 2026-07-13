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

        foreach ($this->rules as $routeId => $defaultRule) {
            // Get value from configuration
            $currentValue = $this->configuration->get($this->getConfigurationKey($routeId), null, $shopConstraint);
            if (is_array($currentValue)) {
                $configResult[$routeId] = $currentValue;
                continue;
            } elseif (is_string($currentValue)) {
                $configResult[$routeId] = array_fill_keys(array_column($this->languages, 'id_lang'), $currentValue);
                continue;
            } else {
                $configResult[$routeId] = array_fill_keys(array_column($this->languages, 'id_lang'), $defaultRule);
                continue;
            }
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
