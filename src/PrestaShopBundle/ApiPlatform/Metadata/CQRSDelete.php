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
use Stringable;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CQRSDelete is a custom operation that provides extra parameters to help configure an operation
 * based on a CQRS command, it is custom tailed for delete operations and forces using the DELETE method.
 * It answers no content, unless a CQRS query is set to read the resource back after the command.
 */
#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
class CQRSDelete extends CQRSCommand
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
        ?string $CQRSCommand = null,
        array $scopes = [],
        ?array $ApiResourceMapping = null,
        ?array $CQRSCommandMapping = null,
        ?bool $experimentalOperation = null,
        ?bool $allowEmptyBody = null,
        ?string $minVersion = null,
        ?string $maxVersion = null,
        ?array $defaultValues = null,
        ?string $CQRSQuery = null,
        ?array $CQRSQueryMapping = null,
    ) {
        $passedArguments = \get_defined_vars();
        $passedArguments['method'] = self::METHOD_DELETE;
        // Usually DELETE operation has nothing to show so no output is needed, unless a query reads the resource back
        // after the command (e.g. a bulk removal of some rows answering the updated parent)
        $hasQuery = !empty($CQRSQuery) || !empty($extraProperties['CQRSQuery']);
        $passedArguments['output'] = $output ?? ($hasQuery ? null : false);
        $passedArguments['status'] = $status ?? ($hasQuery ? Response::HTTP_OK : null);
        // By default, the ReadProvider will trigger a 404 exception with DELETE method, so we disable it unless said otherwise
        $passedArguments['read'] = $read ?? false;
        // For methods POST, PUT and PATCH deserialization is automatically enabled, not with DELETE and we need it to deserialize the command input
        $passedArguments['deserialize'] = $deserialize ?? true;
        // Usually DELETE request don't need a body as the only needed thing is the ID provided in the uri, so we enabled this custom setting,
        // so that empty body is accepted
        $passedArguments['allowEmptyBody'] = $allowEmptyBody ?? $extraProperties['allowEmptyBody'] ?? true;

        parent::__construct(...$passedArguments);
    }
}
