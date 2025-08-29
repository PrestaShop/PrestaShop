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
