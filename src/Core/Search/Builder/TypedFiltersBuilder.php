<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
    public function buildFilters(Filters $filters = null)
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
