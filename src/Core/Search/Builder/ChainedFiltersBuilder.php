<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Search\Builder;

use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * This builder is used to compose other builders, it iterates through its builders
 * set the config for all of them (each one can pick its own parameters), and when
 * building iterates through them overriding the same Filters instance step by step.
 *
 * This allows to split every Filters building into separate classes and then compose
 * them based on your needs.
 */
final class ChainedFiltersBuilder extends AbstractFiltersBuilder
{
    /** @var FiltersBuilderCollection */
    private $builders;

    /**
     * @param array $builders Array of FiltersBuilderInterface
     *
     * @throws \PrestaShop\PrestaShop\Core\Exception\TypeException
     */
    public function __construct(array $builders = [])
    {
        $this->builders = new FiltersBuilderCollection($builders);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config)
    {
        parent::setConfig($config);

        /** @var FiltersBuilderInterface $builder */
        foreach ($this->builders as $builder) {
            $builder->setConfig($config);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function buildFilters(?Filters $filters = null)
    {
        if ($this->builders->count() === 0) {
            return $filters;
        }

        /** @var FiltersBuilderInterface $builder */
        foreach ($this->builders as $builder) {
            $filters = $builder->buildFilters($filters);
        }

        return $filters;
    }
}
