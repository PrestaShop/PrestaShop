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

namespace PrestaShopBundle\ApiPlatform\Metadata\Resource\Factory;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;

/**
 * This factory decorates the ApiPlatform default resource factory. It looks into each operation and checks
 * if the extra property experimentalOperation is set to true, if its is then the operation should be filtered out
 * in production environment. This means the operation is not visible in Swagger, and it's not used to generate the
 * api routing, so it's not usable at all.
 *
 * Scope extraction is also impacted by this filtering, meaning if a scope is only associated to experimental operations
 * it won't be available in prod mode at all.
 *
 * In dev mode all operations are kept though.
 */
class ExperimentalOperationsMetadataCollectionFactoryDecorator implements ResourceMetadataCollectionFactoryInterface
{
    public function __construct(
        private readonly ResourceMetadataCollectionFactoryInterface $innerFactory,
        private readonly bool $isDebug,
    ) {
    }

    public function create(string $resourceClass): ResourceMetadataCollection
    {
        // We call the original method since we only want to alter the result of this method.
        $resourceMetadataCollection = $this->innerFactory->create($resourceClass);

        // In debug mode we filter nothing
        if ($this->isDebug) {
            return $resourceMetadataCollection;
        }

        /** @var ApiResource $resourceMetadata */
        foreach ($resourceMetadataCollection as $resourceMetadata) {
            $operations = $resourceMetadata->getOperations();
            /** @var Operation $operation */
            foreach ($operations as $key => $operation) {
                $extraProperties = $operation->getExtraProperties();
                if (isset($extraProperties['experimentalOperation']) && $extraProperties['experimentalOperation'] === true) {
                    $operations->remove($key);
                }
            }
        }

        return $resourceMetadataCollection;
    }
}
