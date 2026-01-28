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

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
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

        return null;
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
