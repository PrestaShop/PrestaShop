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
     * UrlSchemaDataConfiguration constructor.
     *
     * @param Configuration $configuration
     * @param Context $shopContext
     * @param FeatureInterface $multistoreFeature
     * @param array $rules
     */
    public function __construct(Configuration $configuration, Context $shopContext, FeatureInterface $multistoreFeature, array $rules)
    {
        parent::__construct($configuration, $shopContext, $multistoreFeature);

        $this->rules = $rules;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $configResult = [];
        $shopConstraint = $this->getShopConstraint();

        foreach ($this->rules as $routeId => $defaultRule) {
            $result = $this->configuration->get($this->getConfigurationKey($routeId), null, $shopConstraint) ?: $defaultRule;
            $configResult[$routeId] = $result;
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
            $resolver->setAllowedTypes($ruleId, 'string');
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
