<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use PrestaShopBundle\ApiPlatform\Serializer\CQRSApiSerializer;

trait QueryResultSerializerTrait
{
    protected readonly CQRSApiSerializer $domainSerializer;

    /**
     * @param mixed $CQRSQueryResult this is the QueryResult DTO returned by a CQRS query
     * @param Operation $operation
     * @param array $extraParameters
     *
     * @return mixed It returns the ApiResource DTO object
     */
    protected function denormalizeQueryResult($CQRSQueryResult, Operation $operation, array $extraParameters = [])
    {
        // If the result is a scalar value, then we need to wrap it behind "_queryResult" key and add extra parameters
        // (this could be used in CQRSQueryMapping property to build the DTO)
        if (is_scalar($CQRSQueryResult)) {
            $CQRSQueryResult = array_merge(
                $extraParameters,
                ['_queryResult' => $CQRSQueryResult]
            );
        }

        // Start by normalizing the QueryResult object into normalized array
        $normalizedQueryResult = $this->domainSerializer->normalize($CQRSQueryResult, null, [NormalizationMapper::NORMALIZATION_MAPPING => $this->getCQRSQueryMapping($operation)]);

        if ($operation instanceof CollectionOperationInterface) {
            foreach ($normalizedQueryResult as $key => $result) {
                $normalizedQueryResult[$key] = $this->domainSerializer->denormalize(array_merge($extraParameters, $result), $operation->getClass(), null, [NormalizationMapper::NORMALIZATION_MAPPING => $this->getApiResourceMapping($operation)]);
            }

            return $normalizedQueryResult;
        } else {
            $normalizedQueryResult = array_merge($extraParameters, $normalizedQueryResult);

            return $this->domainSerializer->denormalize($normalizedQueryResult, $operation->getClass(), null, [NormalizationMapper::NORMALIZATION_MAPPING => $this->getApiResourceMapping($operation)]);
        }
    }

    /**
     * Return the mapping used for normalizing AND denormalizing the ApiResource DTO, if specified.
     *
     * @param Operation $operation
     *
     * @return array|null
     */
    protected function getApiResourceMapping(Operation $operation): ?array
    {
        return $operation->getExtraProperties()['ApiResourceMapping'] ?? null;
    }

    /**
     * Return the mapping used for normalizing AND denormalizing the CQRS query, if specified.
     *
     * @param Operation $operation
     *
     * @return array|null
     */
    protected function getCQRSQueryMapping(Operation $operation): ?array
    {
        return $operation->getExtraProperties()['CQRSQueryMapping'] ?? null;
    }

    protected function getCQRSQueryClass(Operation $operation): ?string
    {
        return $operation->getExtraProperties()['CQRSQuery'] ?? null;
    }
}
