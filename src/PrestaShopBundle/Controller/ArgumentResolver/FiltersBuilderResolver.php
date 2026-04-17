<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Controller\ArgumentResolver;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Search\Builder\FiltersBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * This argument resolver uses the FiltersBuilderInterface service to automatically
 * instantiate and inject parameters in controllers.
 */
class FiltersBuilderResolver implements ArgumentValueResolverInterface
{
    /** @var FiltersBuilderInterface */
    private $builder;

    /**
     * @param FiltersBuilderInterface $builder
     */
    public function __construct(FiltersBuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return is_subclass_of($argument->getType(), Filters::class);
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        // The shop constraint should be added in the request attributes by another listener (@see ShopConstraintListener)
        $shopConstraint = null;
        if ($request->attributes->has('shopConstraint') && $request->attributes->get('shopConstraint') instanceof ShopConstraint) {
            $shopConstraint = $request->attributes->get('shopConstraint');
        }

        $this->builder->setConfig([
            'filters_class' => $argument->getType(),
            'request' => $request,
            'shop_constraint' => $shopConstraint,
        ]);

        $filters = $this->builder->buildFilters();

        yield $filters;
    }
}
