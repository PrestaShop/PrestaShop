<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Metadata;

use ApiPlatform\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Parameters;
use ApiPlatform\OpenApi\Attributes\Webhook;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use ApiPlatform\State\OptionsInterface;
use Stringable;

abstract class AbstractCQRSOperation extends AbstractScopedOperation
{
    public function __construct(
        string $method = self::METHOD_GET,
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
        ?string $CQRSQuery = null,
        array $scopes = [],
        ?array $CQRSQueryMapping = null,
        ?array $ApiResourceMapping = null,
        ?bool $experimentalOperation = null,
    ) {
        $passedArguments = \get_defined_vars();

        if (!empty($CQRSQuery)) {
            $this->checkArgumentAndExtraParameterValidity('CQRSQuery', $CQRSQuery, $passedArguments['extraProperties']);
            $passedArguments['extraProperties']['CQRSQuery'] = $CQRSQuery;
        }

        if (!empty($CQRSQueryMapping)) {
            $this->checkArgumentAndExtraParameterValidity('CQRSQueryMapping', $CQRSQueryMapping, $passedArguments['extraProperties']);
            $passedArguments['extraProperties']['CQRSQueryMapping'] = $CQRSQueryMapping;
        }

        // Remove custom arguments
        unset($passedArguments['CQRSQuery']);
        unset($passedArguments['CQRSQueryMapping']);

        parent::__construct(...$passedArguments);
    }

    public function getScopes(): array
    {
        return $this->extraProperties['scopes'] ?? [];
    }

    public function withScopes(array $scopes): static
    {
        $self = clone $this;
        $self->extraProperties['scopes'] = $scopes;

        return $self;
    }

    public function getCQRSQuery(): ?string
    {
        return $this->extraProperties['CQRSQuery'] ?? null;
    }

    public function withCQRSQuery(string $CQRSQuery): static
    {
        $self = clone $this;
        $self->extraProperties['CQRSQuery'] = $CQRSQuery;

        return $self;
    }

    public function getCQRSQueryMapping(): ?array
    {
        return $this->extraProperties['CQRSQueryMapping'] ?? null;
    }

    public function withCQRSQueryMapping(array $CQRSQuery): static
    {
        $self = clone $this;
        $self->extraProperties['CQRSQueryMapping'] = $CQRSQuery;

        return $self;
    }

    public function getApiResourceMapping(): ?array
    {
        return $this->extraProperties['ApiResourceMapping'] ?? null;
    }

    public function withApiResourceMapping(array $CQRSQuery): static
    {
        $self = clone $this;
        $self->extraProperties['ApiResourceMapping'] = $CQRSQuery;

        return $self;
    }

    public function getExperimentalOperation(): bool
    {
        return $this->extraProperties['experimentalOperation'] ?? false;
    }

    public function withExperimentalOperation(bool $experimentalOperation): static
    {
        $self = clone $this;
        $self->extraProperties['experimentalOperation'] = $experimentalOperation;

        return $self;
    }

    protected function checkArgumentAndExtraParameterValidity(string $extraParameterName, $constructorParameterValue, array $extraProperties): void
    {
        if (!empty($extraProperties[$extraParameterName]) && $extraProperties[$extraParameterName] !== $constructorParameterValue) {
            throw new InvalidArgumentException(sprintf(
                'Specifying an extra property %s and a %s argument that are different is invalid',
                $extraParameterName,
                $extraParameterName
            ));
        }
    }
}
