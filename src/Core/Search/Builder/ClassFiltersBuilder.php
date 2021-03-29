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

namespace PrestaShop\PrestaShop\Core\Search\Builder;

use PrestaShop\PrestaShop\Core\Search\Builder\TypedBuilder\TypedFiltersBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters;

/**
 * This builder instantiate a filters object of the specified type using
 * its default values for creation.
 */
final class ClassFiltersBuilder extends AbstractFiltersBuilder
{
    /** @var string */
    private $filtersClass;

    /**
     * @var TypedFiltersBuilderInterface[]
     */
    private $typedBuilders;

    /**
     * @param iterable|null $typedBuilders
     */
    public function __construct(?iterable $typedBuilders = null)
    {
        $this->typedBuilders = [];
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfig(array $config)
    {
        if (isset($config['filters_class'])) {
            $this->filtersClass = $config['filters_class'];
        }
        foreach ($this->typedBuilders as $typedBuilder) {
            $typedBuilder->setConfig($config);
        }

        return parent::setConfig($config);
    }

    /**
     * Build the filters with the class defined by filtersClass
     *
     * @param Filters|null $filters
     *
     * @return Filters
     */
    public function buildFilters(Filters $filters = null)
    {
        if (null === $this->filtersClass) {
            return $filters;
        }

        $typedBuilder = $this->getTypedBuilder();
        if (null !== $typedBuilder) {
            $typedFilters = $typedBuilder->buildFilters($filters);
        } else {
            $typedFilters = $this->buildTypedFilters($filters);
        }

        return $typedFilters;
    }

    /**
     * @param Filters|null $filters
     *
     * @return Filters
     */
    private function buildTypedFilters(?Filters $filters): Filters
    {
        /** @var array $defaultParameters */
        $defaultParameters = call_user_func([$this->filtersClass, 'getDefaults']);
        if (null !== $filters) {
            /** @var Filters $typedFilters */
            $typedFilters = new $this->filtersClass($filters->all(), $filters->getFilterId());
            $typedFilters->add($defaultParameters);
        } else {
            $typedFilters = new $this->filtersClass($defaultParameters, $this->filterId);
        }

        return $typedFilters;
    }

    /**
     * @return TypedFiltersBuilderInterface|null
     */
    private function getTypedBuilder(): ?TypedFiltersBuilderInterface
    {
        foreach ($this->typedBuilders as $typedBuilder) {
            if ($typedBuilder->supports($this->filtersClass)) {
                return $typedBuilder;
            }
        }

        return null;
    }
}
