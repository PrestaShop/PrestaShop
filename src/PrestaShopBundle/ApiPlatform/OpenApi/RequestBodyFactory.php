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

namespace PrestaShopBundle\ApiPlatform\OpenApi;

use ApiPlatform\OpenApi\Model\RequestBody;
use ArrayObject;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSCommand;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;
use Symfony\Component\PropertyInfo\PropertyInitializableExtractorInterface;

class RequestBodyFactory
{
    public function __construct(
        protected readonly PropertyInfoExtractorInterface|PropertyInitializableExtractorInterface $propertyInfoExtractor
    ) {
    }

    public function build(CQRSCommand $operation): ?RequestBody
    {
        if (empty($operation->getCQRSCommand()) || !class_exists($operation->getCQRSCommand())) {
            return null;
        }

        $requestMimeTypes = $this->flattenMimeTypes($operation->getInputFormats() ?: []);
        $inputSchema = [];
        foreach ($requestMimeTypes as $requestMimeType) {
            $operationProperties = $this->getOperationProperties($operation);
            $inputSchema[$requestMimeType] = [
                'schema' => [
                    'type' => 'object',
                    'properties' => $operationProperties,
                ],
            ];
        }

        return new RequestBody(
            description: sprintf('The %s %s resource', 'POST' === $operation->getMethod() ? 'new' : 'updated', $operation->getShortName()),
            content: new ArrayObject($inputSchema),
        );
    }

    private function getOperationProperties(CQRSCommand $operation): array
    {
        $operationProperties = [];
        $classProperties = $this->propertyInfoExtractor->getProperties($operation->getCQRSCommand());
        foreach ($classProperties as $property) {
            if ($this->propertyInfoExtractor->isWritable($operation->getCQRSCommand(), $property)
                || $this->propertyInfoExtractor->isInitializable($operation->getCQRSCommand(), $property)) {
                $propertyTypes = $this->propertyInfoExtractor->getTypes($operation->getCQRSCommand(), $property);
                if (count($propertyTypes) === 1) {
                    $propertyType = $propertyTypes[0];
                    $type = $propertyType->getClassName() ?: $propertyType->getBuiltinType();
                } else {
                    $type = 'mixed';
                }
                $operationProperties[$property] = ['type' => $type];
            }
        }

        return $operationProperties;
    }

    private function flattenMimeTypes(array $responseFormats): array
    {
        $responseMimeTypes = [];
        foreach ($responseFormats as $responseFormat => $mimeTypes) {
            foreach ($mimeTypes as $mimeType) {
                $responseMimeTypes[$mimeType] = $responseFormat;
            }
        }

        return $responseMimeTypes;
    }
}
