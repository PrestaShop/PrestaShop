<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\ApiPlatform\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShopBundle\ApiPlatform\ContextParametersProvider;
use PrestaShopBundle\ApiPlatform\Exception\CQRSQueryNotFoundException;
use PrestaShopBundle\ApiPlatform\NormalizationMapper;
use PrestaShopBundle\ApiPlatform\QueryResultSerializerTrait;
use PrestaShopBundle\ApiPlatform\Serializer\CQRSApiSerializer;
use ReflectionException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class QueryProvider implements ProviderInterface
{
    use QueryResultSerializerTrait;

    public function __construct(
        protected readonly CommandBusInterface $queryBus,
        protected readonly CQRSApiSerializer $domainSerializer,
        protected readonly ContextParametersProvider $contextParametersProvider,
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
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): array|object|null
    {
        $CQRSQueryClass = $this->getCQRSQueryClass($operation);
        if (null === $CQRSQueryClass) {
            throw new CQRSQueryNotFoundException(sprintf('Resource %s has no CQRS query defined.', $operation->getClass()));
        }

        $filters = $context['filters'] ?? [];
        $queryParameters = array_merge($uriVariables, $filters, $this->contextParametersProvider->getContextParameters());

        $CQRSQuery = $this->domainSerializer->denormalize($queryParameters, $CQRSQueryClass, null, [NormalizationMapper::NORMALIZATION_MAPPING => $this->getCQRSQueryMapping($operation)]);
        $CQRSQueryResult = $this->queryBus->handle($CQRSQuery);
        // The result may be null (for DELETE action for example)
        if (null === $CQRSQueryResult) {
            return new ($operation->getClass())();
        }

        return $this->denormalizeQueryResult($CQRSQueryResult, $operation, $queryParameters);
    }
}
