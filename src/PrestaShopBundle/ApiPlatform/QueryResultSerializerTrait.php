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
        // If the result is a scalar value, or a plain list of scalars returned by a non-collection operation, then we
        // need to wrap it behind the "_queryResult" key and add extra parameters (this can be used in the
        // CQRSQueryMapping property to build the DTO). Lists of arrays/objects are left as-is: their elements are
        // mapped individually through the "[@index]" notation, and associative arrays already map their keys to the
        // DTO properties.
        if (is_scalar($CQRSQueryResult)
            || (!$operation instanceof CollectionOperationInterface && is_array($CQRSQueryResult) && $this->isListOfScalars($CQRSQueryResult))
        ) {
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

    /**
     * A non-empty list whose every element is a scalar, e.g. a string[] or int[]. Such a result must be wrapped under
     * "_queryResult" to be mapped onto a single resource property; a list of arrays/objects must not (its elements are
     * mapped individually), and an empty list is left to the resource property default.
     *
     * @param array $result
     */
    private function isListOfScalars(array $result): bool
    {
        if ([] === $result || !array_is_list($result)) {
            return false;
        }

        foreach ($result as $value) {
            if (!is_scalar($value)) {
                return false;
            }
        }

        return true;
    }
}
