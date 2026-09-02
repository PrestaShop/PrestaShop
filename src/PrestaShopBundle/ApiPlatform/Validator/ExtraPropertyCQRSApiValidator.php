<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Validator;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Validator\Exception\ValidationException;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyDefinitionRepositoryInterface;
use PrestaShop\PrestaShop\Core\ExtraProperty\Definition\ExtraPropertyScope;
use PrestaShop\PrestaShop\Core\ExtraProperty\Validation\ExtraPropertyValidatorInterface;
use PrestaShopBundle\ApiPlatform\Exception\LocaleNotFoundException;
use PrestaShopBundle\ApiPlatform\LocalizedValueUpdater;
use PrestaShopBundle\ApiPlatform\Metadata\LocalizedValue;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

/**
 * Admin-API-only decorator of CQRSApiValidator that also validates the incoming `extraProperties` payload and
 * MERGES its violations with the resource constraint violations into a single 422 — instead of one preempting the
 * other.
 *
 * Registered with `decorates: CQRSApiValidator` in the Admin API kernel, so CQRSApiNormalizer (which depends on
 * CQRSApiValidatorInterface) transparently uses it there. Core resource validation in PrestaShop runs during
 * denormalization, so this is the only seam where the two violation lists can be combined.
 */
class ExtraPropertyCQRSApiValidator implements CQRSApiValidatorInterface
{
    public function __construct(
        protected readonly CQRSApiValidatorInterface $inner,
        protected readonly ExtraPropertyDefinitionRepositoryInterface $repository,
        protected readonly ResourceMetadataCollectionFactoryInterface $resourceMetadataFactory,
        protected readonly RequestStack $requestStack,
        protected readonly ExtraPropertyValidatorInterface $validatorAdapter,
        protected readonly LocalizedValueUpdater $localizedValueUpdater,
    ) {
    }

    /**
     * Returns true when the decorated validator reports constraints OR when an extra property definition that
     * declares constraints targets one of the resource's operations — so extra-property validation runs even for
     * resources with no core constraints, and is skipped entirely when no targeted definition has anything to enforce.
     */
    public function hasConstraints(string $resourceClass): bool
    {
        return $this->inner->hasConstraints($resourceClass) || $this->resourceHasExtraProperties($resourceClass);
    }

    public function validate(mixed $apiResource, Operation $operation): void
    {
        $violations = new ConstraintViolationList();

        try {
            $this->inner->validate($apiResource, $operation);
        } catch (ValidationException $e) {
            $violations->addAll($e->getConstraintViolationList());
        }

        $payload = $this->extractExtraPropertiesPayload();
        if (null !== $payload && $operation instanceof HttpOperation) {
            $extraViolations = $this->validateExtraProperties(
                $payload,
                (string) $operation->getUriTemplate(),
                (string) $operation->getMethod(),
            );
            $violations->addAll($extraViolations);
        }

        if ($violations->count() > 0) {
            throw new ValidationException($violations);
        }
    }

    protected function resourceHasExtraProperties(string $resourceClass): bool
    {
        $definitions = $this->repository->getAllDefinitions();
        if ($definitions->isEmpty()) {
            return false;
        }

        try {
            $metadataCollection = $this->resourceMetadataFactory->create($resourceClass);
        } catch (Throwable) {
            return false;
        }

        foreach ($metadataCollection as $resourceMetadata) {
            foreach ($resourceMetadata->getOperations() ?? [] as $operation) {
                if (!$operation instanceof HttpOperation) {
                    continue;
                }
                $uriTemplate = (string) $operation->getUriTemplate();
                if ('' === $uriTemplate) {
                    continue;
                }
                // Only definitions that actually declare constraints make validation worthwhile: a matching
                // definition with no constraints has nothing to enforce, so it does not warrant running validation.
                foreach ($definitions->filterByApi($uriTemplate, (string) $operation->getMethod()) as $definition) {
                    if (!empty($definition->getConstraints())) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Validates an extraProperties payload against the definitions targeting the given operation (URI template +
     * HTTP method). Returns an empty list when nothing matches or the payload is empty. Violations use the path
     * "extraProperties.<module>.<field>[.<locale|shopId>]" so they merge with the resource constraint violations.
     *
     * @param array<string, array<string, mixed>> $extraPropertiesByModule
     */
    protected function validateExtraProperties(array $extraPropertiesByModule, string $uriTemplate, string $method): ConstraintViolationListInterface
    {
        $violations = new ConstraintViolationList();
        if (empty($extraPropertiesByModule)) {
            return $violations;
        }

        $definitions = $this->repository->getAllDefinitions()->filterByApi($uriTemplate, $method);

        // The payload is already grouped [module => [property => value]] — exactly the shape
        // ExtraPropertyValidator::validate() expects. Run it once, then re-base each "<module>.<property>[.<locale>]"
        // path under "extraProperties." (turning Symfony's "[key]" array sub-paths into dotted ".key") so the
        // extra-property violations merge cleanly with the resource constraint violations.
        foreach ($this->validatorAdapter->validate($extraPropertiesByModule, $definitions) as $violation) {
            $path = 'extraProperties.' . str_replace(['[', ']'], ['.', ''], $violation->getPropertyPath());
            $violations->add(new ConstraintViolation(
                $violation->getMessage(),
                $violation->getMessageTemplate(),
                $violation->getParameters(),
                $violation->getRoot(),
                $path,
                $violation->getInvalidValue(),
                $violation->getPlural(),
                $violation->getCode(),
            ));
        }

        // Defensive, key-aware check: a LANG payload may reference a locale that does not exist. Value validation
        // above is key-agnostic, so assert known locales here to return a clean 422 at the extraProperties path
        // rather than failing later during denormalization.
        foreach ($definitions as $definition) {
            if (ExtraPropertyScope::LANG !== $definition->getScope()) {
                continue;
            }
            $value = $extraPropertiesByModule[$definition->getNormalizedModuleKey()][$definition->getPropertyName()] ?? null;
            if (is_array($value)) {
                $this->assertKnownLocales(
                    $violations,
                    $value,
                    $definition->getPropertyName(),
                    sprintf('extraProperties.%s.%s', $definition->getNormalizedModuleKey(), $definition->getPropertyName()),
                );
            }
        }

        return $violations;
    }

    /**
     * Adds a violation when a LANG-scope payload uses a locale that does not exist in the shop.
     *
     * @param array<int|string, mixed> $localizedValue
     */
    protected function assertKnownLocales(ConstraintViolationListInterface $violations, array $localizedValue, string $fieldName, string $basePath): void
    {
        try {
            $this->localizedValueUpdater->denormalizeLocalizedValue(
                $localizedValue,
                $fieldName,
                [LocalizedValue::IS_LOCALIZED_VALUE => true, LocalizedValue::DENORMALIZED_KEY => LocalizedValue::ID_KEY],
            );
        } catch (LocaleNotFoundException $e) {
            $violations->add(new ConstraintViolation($e->getMessage(), $e->getMessage(), [], null, $basePath, $localizedValue));
        }
    }

    /**
     * @return array<string, array<string, mixed>>|null
     */
    protected function extractExtraPropertiesPayload(): ?array
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }

        $content = $request->getContent();
        if ('' === $content) {
            return null;
        }

        $decoded = json_decode($content, true);
        if (!is_array($decoded) || !isset($decoded['extraProperties']) || !is_array($decoded['extraProperties'])) {
            return null;
        }

        return $decoded['extraProperties'];
    }
}
