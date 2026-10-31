<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Processor;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShopBundle\ApiPlatform\ContextParametersProvider;
use PrestaShopBundle\ApiPlatform\Exception\CQRSCommandNotFoundException;
use PrestaShopBundle\ApiPlatform\NormalizationMapper;
use PrestaShopBundle\ApiPlatform\QueryResultSerializerTrait;
use PrestaShopBundle\ApiPlatform\Serializer\CQRSApiSerializer;
use ReflectionException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class CommandProcessor implements ProcessorInterface
{
    use QueryResultSerializerTrait;

    public function __construct(
        protected readonly CommandBusInterface $commandBus,
        protected readonly CQRSApiSerializer $domainSerializer,
        protected readonly ContextParametersProvider $contextParametersProvider,
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

        if ($data instanceof $CQRSCommandClass) {
            $command = $data;
        } else {
            // Start by normalizing the data which should be an ApiPlatform DTO, and merge the URI variables in it as well since the query may contain some extra parameters (like the resource ID)
            $normalizedApiResourceDTO = $this->domainSerializer->normalize($data, null, [NormalizationMapper::NORMALIZATION_MAPPING => $this->getApiResourceMapping($operation)]);
            $commandParameters = array_merge($normalizedApiResourceDTO, $uriVariables, $this->contextParametersProvider->getContextParameters());

            // Denormalize the command and let the bus handle it
            $command = $this->domainSerializer->denormalize($commandParameters, $CQRSCommandClass, null, [NormalizationMapper::NORMALIZATION_MAPPING => $this->getCQRSCommandMapping($operation)]);
        }
        $commandResult = $this->commandBus->handle($command);

        // A collection operation without query answers the list returned by the command, nothing when it returns nothing
        if ($commandResult === null && empty($extraProperties['CQRSQuery']) && $operation instanceof CollectionOperationInterface) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        $allowEmptyBody = $operation->getExtraProperties()['allowEmptyBody'] ?? null;

        // If no result is returned and no query is configured the API returns nothing
        if ($commandResult === null && empty($extraProperties['CQRSQuery']) && ($allowEmptyBody === null || $allowEmptyBody === true)) {
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
        $normalizedCommandResult = [];
        if (!empty($commandResult)) {
            $normalizedCommandResult = $this->domainSerializer->normalize($commandResult, null, [NormalizationMapper::NORMALIZATION_MAPPING => $this->getCQRSCommandMapping($operation)]);
        }
        $queryClass = $this->getCQRSQueryClass($operation);
        if (!$queryClass && $operation instanceof CollectionOperationInterface) {
            return $this->denormalizeApiPlatformDTOCollection($normalizedCommandResult, $operation, $uriVariables);
        }

        // Merge uri variables and normalized command result to have all needed data to create the CQRS query (it probably contains all the needed info to create the CQRS query)
        $normalizedCommandResult = array_merge($uriVariables, $normalizedCommandResult, $this->contextParametersProvider->getContextParameters());

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
        return $this->domainSerializer->denormalize($normalizedCommandResult, $operation->getClass(), null, [NormalizationMapper::NORMALIZATION_MAPPING => $this->getApiResourceMapping($operation)]);
    }

    /**
     * A collection operation without query answers the list returned by the command (e.g. the IDs of the created
     * rows): each element is denormalized into a resource, like the rows of a query result.
     */
    protected function denormalizeApiPlatformDTOCollection(array $normalizedCommandResult, Operation $operation, array $uriVariables): array
    {
        $contextParameters = $this->contextParametersProvider->getContextParameters();
        foreach ($normalizedCommandResult as $key => $result) {
            $normalizedCommandResult[$key] = $this->denormalizeApiPlatformDTO(array_merge($uriVariables, $result, $contextParameters), $operation);
        }

        if (!empty($operation->getExtraProperties()['collectionAsList'])) {
            return array_values($normalizedCommandResult);
        }

        return $normalizedCommandResult;
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
        $CQRSQuery = $this->domainSerializer->denormalize($normalizedCommandResult, $CQRSQueryClass, null, [NormalizationMapper::NORMALIZATION_MAPPING => $this->getCQRSQueryMapping($operation)]);
        $CQRSQueryResult = $this->commandBus->handle($CQRSQuery);

        return $this->denormalizeQueryResult($CQRSQueryResult, $operation, $normalizedCommandResult);
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
