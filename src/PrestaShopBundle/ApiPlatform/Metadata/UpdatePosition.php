<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Metadata;

use ApiPlatform\Metadata\Parameters;
use ApiPlatform\OpenApi\Attributes\Webhook;
use ApiPlatform\OpenApi\Model\Operation as OpenApiOperation;
use ApiPlatform\State\OptionsInterface;
use Attribute;
use PrestaShopBundle\ApiPlatform\Processor\UpdatePositionProcessor;
use Stringable;

/**
 * Class UpdatePosition is a custom operation to update the entities positions.
 * The API Resource itself represent a position modification:
 *  - rowId: int (can be customized via the PositionCollection attribute)
 *  - newPosition: int
 *  - parentId: int (optional, customizable vie $parentIdField)
 *
 * This operation should be used on a dedicated ApiResource class (one operation only)
 * containing a field
 *
 * #[PositionCollection]
 * public array $positions;
 *
 * The attribute allows automatizing the normalization and documentation process.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class UpdatePosition extends AbstractScopedOperation
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
        array $scopes = [],
        ?array $ApiResourceMapping = null,
        ?bool $experimentalOperation = null,
        ?string $positionDefinition = null,
        ?string $parentIdField = null,
    ) {
        $passedArguments = \get_defined_vars();
        $passedArguments['method'] = self::METHOD_PATCH;
        // Disable read listener because it is forced when using PATCH method, but we don't need it since we rely on CQRS commands/queries
        $passedArguments['read'] = $read ?? false;
        // Usually position operation has nothing to show so no output is needed
        $passedArguments['output'] = $output ?? false;
        $passedArguments['processor'] = $processor ?? UpdatePositionProcessor::class;

        if (!empty($positionDefinition)) {
            $this->checkArgumentAndExtraParameterValidity('positionDefinition', $positionDefinition, $passedArguments['extraProperties']);
            $passedArguments['extraProperties']['positionDefinition'] = $positionDefinition;
        }
        unset($passedArguments['positionDefinition']);

        if (!empty($parentIdField)) {
            $this->checkArgumentAndExtraParameterValidity('parentIdField', $parentIdField, $passedArguments['extraProperties']);
            $passedArguments['extraProperties']['parentIdField'] = $parentIdField;
        }
        unset($passedArguments['parentIdField']);

        parent::__construct(...$passedArguments);
    }

    public function getPositionDefinition(): ?string
    {
        return $this->extraProperties['positionDefinition'] ?? null;
    }

    public function withPositionDefinition(string $positionDefinition): static
    {
        $self = clone $this;
        $self->extraProperties['positionDefinition'] = $positionDefinition;

        return $self;
    }

    public function getParentIdField(): ?string
    {
        return $this->extraProperties['parentIdField'] ?? null;
    }

    public function withParentIdField(string $parentIdField): static
    {
        $self = clone $this;
        $self->extraProperties['parentIdField'] = $parentIdField;

        return $self;
    }
}
