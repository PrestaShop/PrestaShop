<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Builder\TypedBuilder;

use PrestaShop\PrestaShop\Core\Language\ContextLanguageProviderInterface;
use PrestaShop\PrestaShop\Core\Search\Builder\AbstractFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShop\PrestaShop\Core\Search\Filters\FeatureValueFilters;
use Symfony\Component\HttpFoundation\Request;

class FeatureValueFiltersBuilder extends AbstractFiltersBuilder implements TypedFiltersBuilderInterface
{
    private Request $request;

    public function __construct(
        protected readonly ContextLanguageProviderInterface $contextLanguageProvider
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config): FeatureValueFiltersBuilder
    {
        $this->request = $config['request'] ?? null;

        return parent::setConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilters(?Filters $filters = null)
    {
        $filterParameters = FeatureValueFilters::getDefaults();
        if (null !== $filters) {
            $filterParameters = array_replace($filterParameters, $filters->all());
        }

        $filterParameters['filters']['feature_id'] = $this->getFeatureId();
        $filterParameters['filters']['language_id'] = $this->contextLanguageProvider->getLanguageId();

        return new FeatureValueFilters($filterParameters);
    }

    /**
     * {@inheritDoc}
     */
    public function supports(string $filterClassName): bool
    {
        return $filterClassName === FeatureValueFilters::class;
    }

    private function getFeatureId(): int
    {
        return $this->request->attributes->getInt('featureId');
    }
}
