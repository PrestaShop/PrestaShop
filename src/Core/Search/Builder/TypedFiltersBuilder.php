<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Search\Builder;

use PrestaShop\PrestaShop\Core\Search\Builder\TypedBuilder\TypedFiltersBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * Class TypedFiltersBuilder is an orchestrator which decided which builder is going to built the strongly
 * typed Filter based on the defined filter class. It loops through a list of typed builders checking if
 * they support the request class and uses the first it finds when a compatibility is found.
 *
 * If no specific type builder is found then the default builder is used.
 */
class TypedFiltersBuilder extends AbstractFiltersBuilder
{
    /**
     * @var FiltersBuilderInterface
     */
    private $defaultBuilder;

    /**
     * @var TypedFiltersBuilderInterface[]
     */
    private $typedBuilders = [];

    /** @var string */
    private $filtersClass;

    /**
     * @var array|null
     */
    private $config = null;

    /**
     * @param FiltersBuilderInterface $defaultBuilder
     * @param iterable|TypedFiltersBuilderInterface[]|null $typedBuilders
     */
    public function __construct(
        FiltersBuilderInterface $defaultBuilder,
        ?iterable $typedBuilders = null
    ) {
        $this->defaultBuilder = $defaultBuilder;

        if (!empty($typedBuilders)) {
            foreach ($typedBuilders as $typedBuilder) {
                $this->addTypedBuilder($typedBuilder);
            }
        }
    }

    /**
     * @param TypedFiltersBuilderInterface $typedFiltersBuilder
     *
     * @return self
     */
    public function addTypedBuilder(TypedFiltersBuilderInterface $typedFiltersBuilder): self
    {
        $this->typedBuilders[] = $typedFiltersBuilder;
        if (null !== $this->config) {
            $typedFiltersBuilder->setConfig($this->config);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config)
    {
        $this->config = $config;

        if (isset($config['filters_class'])) {
            $this->filtersClass = $config['filters_class'];
        }

        $this->defaultBuilder->setConfig($config);
        foreach ($this->typedBuilders as $typedBuilder) {
            $typedBuilder->setConfig($config);
        }

        return parent::setConfig($config);
    }

    /**
     * {@inheritDoc}
     */
    public function buildFilters(?Filters $filters = null)
    {
        $typedBuilder = $this->findTypedBuilder();

        // When a typed builder matches it MUST be used in priority, do not try to manually a filters class that might
        // need some special inputs
        return $typedBuilder ? $typedBuilder->buildFilters($filters) : $this->defaultBuilder->buildFilters($filters);
    }

    /**
     * @return TypedFiltersBuilderInterface|null
     */
    private function findTypedBuilder(): ?TypedFiltersBuilderInterface
    {
        if (empty($this->filtersClass)) {
            return null;
        }

        foreach ($this->typedBuilders as $typedBuilder) {
            if ($typedBuilder->supports($this->filtersClass)) {
                return $typedBuilder;
            }
        }

        return null;
    }
}
