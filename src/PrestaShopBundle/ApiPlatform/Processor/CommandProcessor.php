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

namespace PrestaShopBundle\ApiPlatform\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShopBundle\ApiPlatform\DomainSerializer;
use PrestaShopBundle\ApiPlatform\Exception\NoExtraPropertiesFoundException;
use PrestaShopBundle\ApiPlatform\QueryResultSerializerTrait;
use ReflectionException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class CommandProcessor implements ProcessorInterface
{
    use QueryResultSerializerTrait;

    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly DomainSerializer $apiPlatformSerializer,
    ) {
    }

    /**
     * @param $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     *
     * @return mixed
     *
     * @throws NoExtraPropertiesFoundException
     * @throws ExceptionInterface|ReflectionException
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $extraProperties = $operation->getExtraProperties();
        $commandClass = $extraProperties['command'] ?? null;
        $commandParameters = array_merge($this->apiPlatformSerializer->normalize($data), $uriVariables);

        if (null === $commandClass || !class_exists($commandClass)) {
            throw new NoExtraPropertiesFoundException('Extra property "command" not found');
        }

        $command = $this->apiPlatformSerializer->denormalize($commandParameters, $commandClass);
        $commandResult = $this->commandBus->handle($command);

        // If no result is returned and no query is configured the API returns nothing
        if (empty($commandResult) && empty($extraProperties['query'])) {
            // If the command returns nothing (including void) we return null (because void can't be returned and its value is equivalent to null)
            return null;
        }

        return $this->denormalizeCommandResult($commandResult, $operation, $uriVariables);
    }

    private function denormalizeCommandResult(mixed $commandResult, Operation $operation, array $uriVariables): mixed
    {
        $extraProperties = $operation->getExtraProperties();
        if (!empty($commandResult)) {
            $normalizationMapping = $extraProperties['commandNormalizationMapping'] ?? null;
            $normalizedResult = $this->apiPlatformSerializer->normalize($commandResult, null, [DomainSerializer::NORMALIZATION_MAPPING => $normalizationMapping]);
        } else {
            // Use URI variables as fallback when the command returned no result as it probably contains the ID
            $normalizedResult = $uriVariables;
        }

        $queryClass = $extraProperties['query'] ?? null;
        // If no query class as specified the normalized data is simply what the command returned (an array, an object, ...) that is
        // denormalized to match the operation class
        if (!$queryClass) {
            return $this->apiPlatformSerializer->denormalize($normalizedResult, $operation->getClass());
        }

        // If a query was specified it means the expected return should use it, usually it allows returning the full object like in GET
        // operation, but it can also be q different query that returns different data from the GET
        $query = $this->apiPlatformSerializer->denormalize($normalizedResult, $queryClass);
        $queryResult = $this->commandBus->handle($query);

        return $this->denormalizeQueryResult($queryResult, $operation);
    }
}
