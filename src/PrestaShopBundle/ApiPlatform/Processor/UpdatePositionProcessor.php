<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\ApiPlatform\Processor;

use ApiPlatform\Metadata\Exception\InvalidArgumentException;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\Exception\PositionUpdateException;
use PrestaShop\PrestaShop\Core\Grid\Position\GridPositionUpdaterInterface;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionDefinition;
use PrestaShop\PrestaShop\Core\Grid\Position\PositionUpdateFactoryInterface;
use PrestaShopBundle\ApiPlatform\Exception\PositionDefinitionNotFoundException;
use PrestaShopBundle\ApiPlatform\Exception\PositionDefinitionParentIdNotFoundException;
use PrestaShopBundle\ApiPlatform\NormalizationMapper;
use PrestaShopBundle\ApiPlatform\Serializer\CQRSApiSerializer;
use Psr\Container\ContainerInterface;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;

class UpdatePositionProcessor implements ProcessorInterface
{
    public function __construct(
        protected readonly ContainerInterface $container,
        protected readonly PositionUpdateFactoryInterface $positionUpdateFactory,
        protected readonly GridPositionUpdaterInterface $gridPositionUpdater,
        protected readonly CQRSApiSerializer $domainSerializer,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $positionDefinitionName = $operation->getExtraProperties()['positionDefinition'] ?? null;
        if (null === $positionDefinitionName) {
            throw new PositionDefinitionNotFoundException(sprintf('Resource %s has no position definition defined.', $operation->getClass()));
        }

        if (!$this->container->has($positionDefinitionName)) {
            // We use UnexpectedValueException as it will be caught by API Platform and interpreted as a 400 http error, similar to the behaviour
            // for CQRS queries and commands not found
            throw new UnexpectedValueException(sprintf('PositionDefinition service %s does not exist.', $positionDefinitionName));
        }

        $normalizedData = $this->domainSerializer->normalize($data, null, [NormalizationMapper::NORMALIZATION_MAPPING => $this->getApiResourceMapping($operation)]);

        /** @var PositionDefinition $positionDefinition */
        $positionDefinition = $this->container->get($positionDefinitionName);
        $parentIdField = $operation->getExtraProperties()['parentIdField'] ?? 'parentId';
        $positionsData = $this->getPositionsData($normalizedData, $positionDefinition, $parentIdField);

        try {
            $positionUpdate = $this->positionUpdateFactory->buildPositionUpdate($positionsData, $positionDefinition);
            $this->gridPositionUpdater->update($positionUpdate);
        } catch (PositionUpdateException $e) {
            // We catch and force the API Platform exception to have a 400 bad request code
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }
    }

    protected function getPositionsData(array $data, PositionDefinition $positionDefinition, string $parentIdField): ?array
    {
        $positionsData = [
            'positions' => $data['positions'],
        ];

        // Check that parentId is present in the data
        if ($positionDefinition->getParentIdField() !== null) {
            if (!isset($data[$parentIdField])) {
                throw new PositionDefinitionParentIdNotFoundException(sprintf(
                    'Position definition expects %s field in data, maybe check your ApiResourceMapping.',
                    $parentIdField,
                ));
            } else {
                $positionsData['parentId'] = $data[$parentIdField];
            }
        }

        return $positionsData;
    }

    protected function getApiResourceMapping(Operation $operation): ?array
    {
        return $operation->getExtraProperties()['ApiResourceMapping'] ?? null;
    }
}
