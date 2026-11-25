<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Meta;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Exception\CoreException;
use PrestaShop\PrestaShop\Core\Seo\NoIndexUrlParametersMatcher;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SEOOptionsDataConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = [
        'product_attributes_in_title',
        'noindex_url_parameters_list',
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'product_attributes_in_title' => (bool) $this->configuration->get('PS_PRODUCT_ATTRIBUTES_IN_TITLE', false, $shopConstraint),
            'noindex_url_parameters_list' => (string) $this->configuration->get('PS_NOINDEX_URL_PARAMETERS_LIST', '', $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];
        // Callers written before the noindex list existed only send the historical field.
        // Outside these contexts a missing field means "inherit", so nothing must be added.
        if (!$this->multistoreFeature->isUsed() || $this->shopContext->isAllShopContext()) {
            $configuration += ['noindex_url_parameters_list' => $this->getConfiguration()['noindex_url_parameters_list']];
        }

        try {
            if ($this->validateConfiguration($configuration)) {
                $shopConstraint = $this->getShopConstraint();
                if (array_key_exists('noindex_url_parameters_list', $configuration)) {
                    $configuration['noindex_url_parameters_list'] = implode(
                        NoIndexUrlParametersMatcher::PARAMETERS_SEPARATOR,
                        NoIndexUrlParametersMatcher::parseParameterNames((string) $configuration['noindex_url_parameters_list'])
                    );
                }

                $this->updateConfigurationValue('PS_PRODUCT_ATTRIBUTES_IN_TITLE', 'product_attributes_in_title', $configuration, $shopConstraint);
                $this->updateConfigurationValue('PS_NOINDEX_URL_PARAMETERS_LIST', 'noindex_url_parameters_list', $configuration, $shopConstraint);
            }
        } catch (CoreException $exception) {
            $errors[] = $exception->getMessage();
        }

        return $errors;
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $resolver = (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('product_attributes_in_title', 'bool')
            ->setAllowedTypes('noindex_url_parameters_list', ['string', 'null']);

        return $resolver;
    }
}
