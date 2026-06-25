<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Validator;

use ApiPlatform\Metadata\Operation;

/**
 * Contract for the CQRS API validator: it tells whether a resource needs validation (hasConstraints) and validates
 * a denormalized API resource against the operation's constraints (validate).
 *
 * Implemented by CQRSApiValidator and decorated, in the Admin API kernel only, by ExtraPropertyCQRSApiValidator
 * (which merges extra-property violations with the resource constraint violations).
 */
interface CQRSApiValidatorInterface
{
    public function hasConstraints(string $resourceClass): bool;

    public function validate(mixed $apiResource, Operation $operation): void;
}
