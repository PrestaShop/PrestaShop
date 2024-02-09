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

namespace PrestaShopBundle\ApiPlatform\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Context\LanguageContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShopBundle\ApiPlatform\ContextParametersTrait;
use PrestaShopBundle\ApiPlatform\DomainSerializer;
use PrestaShopBundle\ApiPlatform\Exception\CQRSQueryNotFoundException;
use PrestaShopBundle\ApiPlatform\QueryResultSerializerTrait;
use ReflectionException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class QueryProvider implements ProviderInterface
{
    use QueryResultSerializerTrait;
    use ContextParametersTrait;

    public function __construct(
        protected readonly CommandBusInterface $queryBus,
        protected readonly DomainSerializer $domainSerializer,
        protected readonly ShopContext $shopContext,
        protected readonly LanguageContext $languageContext
    ) {
    }

    /**
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     *
     * @return array|object|null
     *
     * @throws ExceptionInterface
     * @throws CQRSQueryNotFoundException
     * @throws ReflectionException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|null|object
    {
        $CQRSQueryClass = $this->getCQRSQueryClass($operation);
        if (null === $CQRSQueryClass) {
            throw new CQRSQueryNotFoundException(sprintf('Resource %s has no CQRS query defined.', $operation->getClass()));
        }

        $filters = $context['filters'] ?? [];
        $queryParameters = array_merge($uriVariables, $filters, $this->getContextParameters());

        $CQRSQuery = $this->domainSerializer->denormalize($queryParameters, $CQRSQueryClass, null, [DomainSerializer::NORMALIZATION_MAPPING => $this->getCQRSQueryMapping($operation)]);
        $CQRSQueryResult = $this->queryBus->handle($CQRSQuery);

        return $this->denormalizeQueryResult($CQRSQueryResult, $operation);
    }
}
