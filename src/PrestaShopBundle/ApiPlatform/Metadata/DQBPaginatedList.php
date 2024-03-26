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

namespace PrestaShopBundle\ApiPlatform\Metadata;

use ApiPlatform\Metadata\CollectionOperationInterface;
use PrestaShop\PrestaShop\Core\Search\Filters;
use PrestaShopBundle\ApiPlatform\Provider\QueryListProvider;

/**
 * Class DoctrineQueryBuilderPaginatedList is a custom operation that provides extra parameters
 * to help configure an operation based on a GetCollection,
 * it is custom tailed for read operations and forces using the GET method.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class DQBPaginatedList extends AbstractCQRSOperation implements CollectionOperationInterface
{
    public function __construct(
        ?string $uriTemplate = null,
        ?array $types = null,
                $formats = null,
                $inputFormats = null,
                $outputFormats = null,
                $uriVariables = null,
        ?string $routePrefix = null,
        ?string $routeName = null,
        ?array $defaults = null,
        ?array $requirements = null,
        ?array $options = null,
        ?bool $stateless = null,
        ?string $sunset = null,
        ?string $acceptPatch = null,
                $status = null,
        ?string $host = null,
        ?array $schemes = null,
        ?string $condition = null,
        ?string $controller = null,
        ?array $cacheHeaders = null,
        ?array $hydraContext = null,
        ?array $openapiContext = null,
        ?bool $openapi = null,
        ?array $exceptionToStatus = null,
        ?bool $queryParameterValidationEnabled = null,
        ?string $shortName = null,
        ?string $class = null,
        ?bool $paginationEnabled = null,
        ?string $paginationType = null,
        ?int $paginationItemsPerPage = null,
        ?int $paginationMaximumItemsPerPage = null,
        ?bool $paginationPartial = null,
        ?bool $paginationClientEnabled = null,
        ?bool $paginationClientItemsPerPage = null,
        ?bool $paginationClientPartial = null,
        ?bool $paginationFetchJoinCollection = null,
        ?bool $paginationUseOutputWalkers = null,
        ?array $paginationViaCursor = null,
        ?array $order = null,
        ?string $description = null,
        ?array $normalizationContext = null,
        ?array $denormalizationContext = null,
        ?string $security = null,
        ?string $securityMessage = null,
        ?string $securityPostDenormalize = null,
        ?string $securityPostDenormalizeMessage = null,
        ?string $securityPostValidation = null,
        ?string $securityPostValidationMessage = null,
        ?string $deprecationReason = null,
        ?array $filters = null,
        ?array $validationContext = null,
                $input = null,
                $output = null,
                $mercure = null,
                $messenger = null,
        ?bool $elasticsearch = null,
        ?int $urlGenerationStrategy = null,
        ?bool $read = null,
        ?bool $deserialize = null,
        ?bool $validate = null,
        ?bool $write = null,
        ?bool $serialize = null,
        ?bool $fetchPartial = null,
        ?bool $forceEager = null,
        ?int $priority = null,
        ?string $name = null,
                $provider = null,
                $processor = null,
        array $extraProperties = [],
        array $scopes = [],
        ?array $ApiResourceMapping = null,
        ?string $queryBuilder = null,
        ?string $filtersClass = null,
        ?array $filtersMapping = null,
    ) {
        $passedArguments = \get_defined_vars();
        $passedArguments['method'] = self::METHOD_GET;
        $passedArguments['provider'] = $provider ?? QueryListProvider::class;
        $passedArguments['filtersClass'] = $filtersClass ?? Filters::class;

        if (!empty($queryBuilder)) {
            $this->checkArgumentAndExtraParameterValidity('queryBuilder', $queryBuilder, $passedArguments['extraProperties']);
            $passedArguments['extraProperties']['queryBuilder'] = $queryBuilder;
        }

        if (!empty($filtersClass)) {
            $this->checkArgumentAndExtraParameterValidity('filtersClass', $filtersClass, $passedArguments['extraProperties']);
            $passedArguments['extraProperties']['filtersClass'] = $filtersClass;
        }

        if (!empty($filtersMapping)) {
            $this->checkArgumentAndExtraParameterValidity('filtersMapping', $filtersMapping, $passedArguments['extraProperties']);
            $passedArguments['extraProperties']['filtersMapping'] = $filtersMapping;
        }

        unset($passedArguments['queryBuilder']);
        unset($passedArguments['filtersClass']);
        unset($passedArguments['filtersMapping']);

        parent::__construct(...$passedArguments);
    }

    public function getQueryBuilder(): ?string
    {
        return $this->extraProperties['queryBuilder'] ?? null;
    }

    public function withQueryBuilder(string $queryBuilder): static
    {
        $self = clone $this;
        $self->extraProperties['queryBuilder'] = $queryBuilder;

        return $self;
    }

    public function getFiltersClass(): ?string
    {
        return $this->extraProperties['filtersClass'];
    }

    public function withFiltersClass(string $filtersClass): static
    {
        $self = clone $this;
        $self->extraProperties['filtersClass'] = $filtersClass;

        return $self;
    }

    public function getFiltersMapping(): ?array
    {
        return $this->extraProperties['filtersMapping'];
    }

    public function withFiltersMapping(array $filtersMapping): static
    {
        $self = clone $this;
        $self->extraProperties['filtersMapping'] = $filtersMapping;

        return $self;
    }
}
