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

use PrestaShopBundle\ApiPlatform\Processor\CommandProcessor;
use PrestaShopBundle\ApiPlatform\Provider\QueryProvider;

/**
 * Class CQRSCommand handles parameters to ease the configuration of an operation relying on CommandProcessor
 * it doesn't force any arguments, so it is suitable for custom usage, but it is recommended to use CQRSCreate
 * or CQRSUpdate instead as they handle default values that help the configuration and avoid unexpected behaviour.
 */
#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::IS_REPEATABLE)]
class CQRSCommand extends AbstractCQRSOperation
{
    public function __construct(
        string $method = self::METHOD_POST,
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
        ?string $CQRSCommand = null,
        ?string $CQRSQuery = null,
        array $scopes = [],
        ?array $CQRSQueryMapping = null,
        ?array $ApiResourceMapping = null,
        ?array $CQRSCommandMapping = null,
    ) {
        $passedArguments = \get_defined_vars();

        $passedArguments['processor'] = $processor ?? CommandProcessor::class;

        if (!empty($CQRSCommand)) {
            $this->checkArgumentAndExtraParameterValidity('CQRSCommand', $CQRSCommand, $passedArguments['extraProperties']);
            $passedArguments['extraProperties']['CQRSCommand'] = $CQRSCommand;
        }

        if (!empty($CQRSCommandMapping)) {
            $this->checkArgumentAndExtraParameterValidity('CQRSCommandMapping', $CQRSCommandMapping, $passedArguments['extraProperties']);
            $passedArguments['extraProperties']['CQRSCommandMapping'] = $CQRSCommandMapping;
        }

        // If a CQRSQuery is specified without a provider we use the QueryProvider by default
        if (!empty($CQRSQuery) || !empty($passedArguments['extraProperties']['CQRSQuery'])) {
            $passedArguments['provider'] = $provider ?? QueryProvider::class;
        }

        // Remove custom arguments
        unset($passedArguments['CQRSCommand']);
        unset($passedArguments['CQRSCommandMapping']);

        parent::__construct(...$passedArguments);
    }

    public function getCQRSCommand(): ?string
    {
        return $this->extraProperties['CQRSCommand'] ?? null;
    }

    public function withCQRSCommand(string $CQRSCommand): static
    {
        $self = clone $this;
        $self->extraProperties['CQRSCommand'] = $CQRSCommand;

        return $self;
    }

    public function withCQRSQuery(string $CQRSQuery): static
    {
        $self = clone $this;
        $self->extraProperties['CQRSQuery'] = $CQRSQuery;
        if (empty($self->provider)) {
            $self->provider = QueryProvider::class;
        }

        return $self;
    }

    public function getCQRSCommandMapping(): ?array
    {
        return $this->extraProperties['CQRSCommandMapping'] ?? null;
    }

    public function withCQRSCommandMapping(array $CQRSQuery): static
    {
        $self = clone $this;
        $self->extraProperties['CQRSCommandMapping'] = $CQRSQuery;

        return $self;
    }
}
