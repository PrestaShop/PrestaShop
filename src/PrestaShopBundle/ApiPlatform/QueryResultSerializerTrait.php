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

namespace PrestaShopBundle\ApiPlatform;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;

trait QueryResultSerializerTrait
{
    protected readonly DomainSerializer $domainSerializer;

    /**
     * @param mixed $CQRSQueryResult this is the QueryResult DTO returned by a CQRS query
     * @param Operation $operation
     *
     * @return mixed It returns the ApiResource DTO object
     */
    protected function denormalizeQueryResult($CQRSQueryResult, Operation $operation)
    {
        // Start by normalizing the QueryResult object into normalized array
        $normalizedQueryResult = $this->domainSerializer->normalize($CQRSQueryResult, null, [DomainSerializer::NORMALIZATION_MAPPING => $this->getCQRSQueryMapping($operation)]);

        if ($operation instanceof CollectionOperationInterface) {
            foreach ($normalizedQueryResult as $key => $result) {
                $normalizedQueryResult[$key] = $this->domainSerializer->denormalize($result, $operation->getClass(), null, [DomainSerializer::NORMALIZATION_MAPPING => $this->getApiResourceMapping($operation)]);
            }

            return $normalizedQueryResult;
        }

        return $this->domainSerializer->denormalize($normalizedQueryResult, $operation->getClass(), null, [DomainSerializer::NORMALIZATION_MAPPING => $this->getApiResourceMapping($operation)]);
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
