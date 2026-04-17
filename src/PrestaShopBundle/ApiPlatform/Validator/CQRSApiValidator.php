<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\ApiPlatform\Validator;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Validator\ValidatorInterface;
use Symfony\Component\Validator\Mapping\ClassMetadataInterface;
use Symfony\Component\Validator\Mapping\Factory\MetadataFactoryInterface;

/**
 * Because the core CQRS commands have no constraints defined in their classes, we need to define them on the associated API
 * Resource class, but since the resource carries the constraints the resource needs to be the one validated, so this service
 * is used right before the CQRS object denormalization to validate the input.
 */
class CQRSApiValidator
{
    public function __construct(
        protected readonly MetadataFactoryInterface $validatorMetadataFactory,
        protected readonly ValidatorInterface $validator,
    ) {
    }

    public function hasConstraints(string $resourceClass): bool
    {
        if (!$this->validatorMetadataFactory->hasMetadataFor($resourceClass)) {
            return false;
        }

        $resourceMetadata = $this->validatorMetadataFactory->getMetadataFor($resourceClass);

        return
            !empty($resourceMetadata->getConstraints())
            || ($resourceMetadata instanceof ClassMetadataInterface && !empty($resourceMetadata->getConstrainedProperties()))
        ;
    }

    public function validate(mixed $apiResource, Operation $operation): void
    {
        $this->validator->validate($apiResource, $operation->getValidationContext() ?? []);
    }
}
