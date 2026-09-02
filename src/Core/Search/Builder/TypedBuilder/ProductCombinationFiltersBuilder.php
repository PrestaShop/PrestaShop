<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Builder\TypedBuilder;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use PrestaShop\PrestaShop\Core\Search\Builder\AbstractFiltersBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShop\PrestaShop\Core\Search\Filters\ProductCombinationFilters;
use Symfony\Component\HttpFoundation\Request;

/**
 * This builder is specific to ProductCombinationFilters, which have a mandatory filter criteria product_id
 * that must be applied. This builder is able to fetch it from Request attribute so that it can be used in
 * the ProductCombinationFilters constructor as expected.
 */
class ProductCombinationFiltersBuilder extends AbstractFiltersBuilder implements TypedFiltersBuilderInterface
{
    /** @var Request */
    private $request;

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config)
    {
        $this->request = $config['request'] ?? null;

        return parent::setConfig($config);
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilters(?Filters $filters = null)
    {
        $filterParameters = ProductCombinationFilters::getDefaults();
        if (null !== $filters) {
            $filterParameters = array_replace($filterParameters, $filters->all());
        }

        $productId = $this->getProductId();
        $filterParameters['filters']['product_id'] = $productId;

        return new ProductCombinationFilters($this->buildShopConstraint(), $filterParameters);
    }

    /**
     * Fetch the product ID from request attributes (based on routing attribute since the product ID is in the URL)
     * This method might need to evolve if the ID were to passed differently (GET or POST for example).
     *
     * @return int
     */
    private function getProductId(): int
    {
        return $this->request->attributes->getInt('productId');
    }

    /**
     * Build ShopConstraint from request. These filters only supports single shop constraint.
     *
     * @return ShopConstraint
     */
    private function buildShopConstraint(): ShopConstraint
    {
        if ($this->request->query->has('shopId')) {
            return ShopConstraint::shop($this->request->query->getInt('shopId'));
        }
        if ($this->request->attributes->has('shopConstraint')) {
            return $this->request->attributes->get('shopConstraint');
        }

        throw new InvalidArgumentException('Missing "shopId" in request for combinations list filters');
    }

    /**
     * {@inheritDoc}
     */
    public function supports(string $filterClassName): bool
    {
        return $filterClassName === ProductCombinationFilters::class;
    }
}
