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
use PrestaShop\PrestaShop\Core\Context\ApiClientContext;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShopBundle\ApiPlatform\ContextParametersTrait;
use PrestaShopBundle\ApiPlatform\DomainSerializer;
use PrestaShopBundle\ApiPlatform\Exception\CQRSCommandNotFoundException;
use PrestaShopBundle\ApiPlatform\QueryResultSerializerTrait;
use ReflectionException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class CommandProcessor implements ProcessorInterface
{
    use QueryResultSerializerTrait;
    use ContextParametersTrait;

    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly DomainSerializer $domainSerializer,
        protected readonly ShopContext $shopContext,
        protected readonly LanguageContext $languageContext,
        protected readonly ApiClientContext $apiClientContext,
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
     * @throws CQRSCommandNotFoundException
     * @throws ExceptionInterface|ReflectionException
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $extraProperties = $operation->getExtraProperties();
        $CQRSCommandClass = $this->getCQRSCommandClass($operation);
        if (null === $CQRSCommandClass || !class_exists($CQRSCommandClass)) {
            throw new CQRSCommandNotFoundException(sprintf('Resource %s has no CQRS command defined.', $operation->getClass()));
        }

        // Start by normalizing the data which should be an ApiPlatform DTO, and merge the URI variables in it as well since the query may contain some extra parameters (like the resource ID)
        $normalizedApiResourceDTO = $this->domainSerializer->normalize($data, null, [DomainSerializer::NORMALIZATION_MAPPING => $this->getApiResourceMapping($operation)]);
        $commandParameters = array_merge($normalizedApiResourceDTO, $uriVariables, $this->getContextParameters());

        // Denormalize the command and let the bus handle it
        $command = $this->domainSerializer->denormalize($commandParameters, $CQRSCommandClass, null, [DomainSerializer::NORMALIZATION_MAPPING => $this->getCQRSCommandMapping($operation)]);
        $commandResult = $this->commandBus->handle($command);

        // If no result is returned and no query is configured the API returns nothing
        if (empty($commandResult) && empty($extraProperties['CQRSQuery'])) {
            // If the command returns nothing (including void) we return null (because void can't be returned and its value is equivalent to null)
            return null;
        }

        return $this->denormalizeCommandResult($commandResult, $operation, $uriVariables);
    }

    /**
     * Transform CQRS result into an ApiPlatform DTO object.
     *
     * @param mixed $commandResult
     * @param Operation $operation
     * @param array $uriVariables
     *
     * @return mixed
     */
    protected function denormalizeCommandResult(mixed $commandResult, Operation $operation, array $uriVariables): mixed
    {
        if (!empty($commandResult)) {
            $normalizedCommandResult = $this->domainSerializer->normalize($commandResult, null, [DomainSerializer::NORMALIZATION_MAPPING => $this->getCQRSCommandMapping($operation)]);
        } else {
            // Use URI variables as fallback when the command returned no result as it probably contains the ID that will be needed to create the CQRS query
            $normalizedCommandResult = $uriVariables;
        }
        $normalizedCommandResult = array_merge($normalizedCommandResult, $this->getContextParameters());

        $queryClass = $this->getCQRSQueryClass($operation);
        if (!$queryClass) {
            return $this->denormalizeApiPlatformDTO($normalizedCommandResult, $operation);
        }

        return $this->handleCQRSQueryAndReturnResult($queryClass, $normalizedCommandResult, $operation);
    }

    /**
     * If no query class as specified the normalized data is simply what the command returned (an array, an object, ...) that is
     * denormalized to match the operation class
     *
     * @param array $normalizedCommandResult
     * @param Operation $operation
     *
     * @return mixed
     */
    protected function denormalizeApiPlatformDTO(array $normalizedCommandResult, Operation $operation): mixed
    {
        return $this->domainSerializer->denormalize($normalizedCommandResult, $operation->getClass(), null, [DomainSerializer::NORMALIZATION_MAPPING => $this->getApiResourceMapping($operation)]);
    }

    /**
     * If a query was specified it means the expected return should use it, usually it allows returning the full object like in GET
     * operation, but it could also be a different query that returns different data from the GET to return a small piece of the object for example.
     *
     * @param string $CQRSQueryClass
     * @param array $normalizedCommandResult
     * @param Operation $operation
     *
     * @return mixed
     */
    protected function handleCQRSQueryAndReturnResult(string $CQRSQueryClass, array $normalizedCommandResult, Operation $operation): mixed
    {
        $CQRSQuery = $this->domainSerializer->denormalize($normalizedCommandResult, $CQRSQueryClass, null, [DomainSerializer::NORMALIZATION_MAPPING => $this->getCQRSQueryMapping($operation)]);
        $CQRSQueryResult = $this->commandBus->handle($CQRSQuery);

        return $this->denormalizeQueryResult($CQRSQueryResult, $operation);
    }

    /**
     * Return the mapping used for normalizing AND denormalizing the CQRS command, if specified.
     *
     * @param Operation $operation
     *
     * @return array|null
     */
    protected function getCQRSCommandMapping(Operation $operation): ?array
    {
        return $operation->getExtraProperties()['CQRSCommandMapping'] ?? null;
    }

    protected function getCQRSCommandClass(Operation $operation): ?string
    {
        return $operation->getExtraProperties()['CQRSCommand'] ?? null;
    }
}
