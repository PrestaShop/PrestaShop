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

use ApiPlatform\Metadata\Parameters;
use ApiPlatform\OpenApi\Attributes\Webhook;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use ApiPlatform\State\OptionsInterface;
use Attribute;
use PrestaShopBundle\ApiPlatform\Processor\CommandProcessor;
use Stringable;

/**
 * Class CQRSCommand handles parameters to ease the configuration of an operation relying on CommandProcessor
 * it doesn't force any arguments, so it is suitable for custom usage, but it is recommended to use CQRSCreate
 * or CQRSUpdate instead as they handle default values that help the configuration and avoid unexpected behaviour.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
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
        ?array $headers = null,
        ?array $cacheHeaders = null,
        ?array $paginationViaCursor = null,
        ?array $hydraContext = null,
        ?array $openapiContext = null,
        bool|OpenApiOperation|Webhook|null $openapi = null,
        ?array $exceptionToStatus = null,
        ?array $links = null,
        ?array $errors = null,

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
        ?array $order = null,
        ?string $description = null,
        ?array $normalizationContext = null,
        ?array $denormalizationContext = null,
        ?bool $collectDenormalizationErrors = null,
        string|Stringable|null $security = null,
        ?string $securityMessage = null,
        string|Stringable|null $securityPostDenormalize = null,
        ?string $securityPostDenormalizeMessage = null,
        string|Stringable|null $securityPostValidation = null,
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
        ?OptionsInterface $stateOptions = null,
        array|Parameters|null $parameters = null,
        ?bool $queryParameterValidationEnabled = null,
        array $extraProperties = [],
        ?string $CQRSCommand = null,
        ?string $CQRSQuery = null,
        array $scopes = [],
        ?array $CQRSQueryMapping = null,
        ?array $ApiResourceMapping = null,
        ?array $CQRSCommandMapping = null,
        ?bool $experimentalOperation = null,
        ?bool $allowEmptyBody = null,
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

        if ($allowEmptyBody !== null) {
            $this->checkArgumentAndExtraParameterValidity('allowEmptyBody', $allowEmptyBody, $passedArguments['extraProperties']);
            $passedArguments['extraProperties']['allowEmptyBody'] = $allowEmptyBody;
        }

        // Remove custom arguments
        unset($passedArguments['CQRSCommand']);
        unset($passedArguments['CQRSCommandMapping']);
        unset($passedArguments['allowEmptyBody']);

        // By default, the CQRS command is used as the input base class as it contains the exact available parameters for this operation
        // Exception in case the class doesn't exist we don't force the input because InputOutputResourceMetadataCollectionFactory will raise an exception when the resources are parsed
        if (empty($passedArguments['input']) && !empty($passedArguments['extraProperties']['CQRSCommand']) && class_exists($passedArguments['extraProperties']['CQRSCommand'])) {
            $passedArguments['input'] = $passedArguments['extraProperties']['CQRSCommand'];
        }

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
        // Test if the input was a copy of the CQRSCommand extra property, set in the constructor (if none was set then we can copy it as well)
        if (empty($this->input) || empty($this->extraProperties['CQRSCommand']) || $this->input === $this->extraProperties['CQRSCommand']) {
            $self->input = $CQRSCommand;
        }

        return $self;
    }

    public function withCQRSQuery(string $CQRSQuery): static
    {
        $self = clone $this;
        $self->extraProperties['CQRSQuery'] = $CQRSQuery;

        return $self;
    }

    public function getCQRSCommandMapping(): ?array
    {
        return $this->extraProperties['CQRSCommandMapping'] ?? null;
    }

    public function withCQRSCommandMapping(array $CQRSCommandMapping): static
    {
        $self = clone $this;
        $self->extraProperties['CQRSCommandMapping'] = $CQRSCommandMapping;

        return $self;
    }

    public function getAllowEmptyBody(): ?bool
    {
        return $this->extraProperties['allowEmptyBody'] ?? null;
    }

    public function withAllowEmptyBody(bool $allowEmptyBody): static
    {
        $self = clone $this;
        $self->extraProperties['allowEmptyBody'] = $allowEmptyBody;

        return $self;
    }
}
