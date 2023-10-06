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

namespace PrestaShopBundle\ApiPlatform\Decorator;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Resource\Factory\ResourceMetadataCollectionFactoryInterface;
use ApiPlatform\Metadata\Resource\ResourceMetadataCollection;

/** Decorate Api platform's ResourceMetadataCollectionFactory, so we can alter the results it returns */
class ScopesToSecurityMetadataCollectionFactoryDecorator implements ResourceMetadataCollectionFactoryInterface
{
    private ResourceMetadataCollectionFactoryInterface $innerFactory;

    public function __construct(ResourceMetadataCollectionFactoryInterface $innerFactory)
    {
        $this->innerFactory = $innerFactory;
    }

    public function create(string $resourceClass): ResourceMetadataCollection
    {
        // We call the original method since we only want to alter the result of this method.
        $resourceMetadataCollection = $this->innerFactory->create($resourceClass);

        /** @var ApiResource $resourceMetadata */
        foreach ($resourceMetadataCollection as $resourceMetadata) {
            $operations = $resourceMetadata->getOperations();
            /** @var Operation $operation */
            foreach ($operations as $key => $operation) {
                $extraProperties = $operation->getExtraProperties();
                if (array_key_exists('scopes', $extraProperties)) {
                    // We remove the original element and replace it with our modified clone.
                    $operations->remove($key);
                    $operations->add($key, $operation->withSecurity(
                        $this->translateScopeToSecurity($extraProperties['scopes'], $operation->getSecurity())
                    ));
                }
            }
        }

        return $resourceMetadataCollection;
    }

    private function translateScopeToSecurity(array $scopes, ?string $existingSecurity): string
    {
        $security = '';
        $arrayLength = count($scopes);
        foreach ($scopes as $key => $scope) {
            $security .= 'is_granted("ROLE_' . strtoupper($scope) . '")';
            if ($key !== $arrayLength - 1) {
                $security .= ' OR ';
            }
        }

        return empty($existingSecurity) ? $security : sprintf('(%s) OR (%s)', $existingSecurity, $security);
    }
}
