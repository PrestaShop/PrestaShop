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
use PrestaShopBundle\ApiPlatform\Exception\NoExtraPropertiesFoundException;
use PrestaShopBundle\ApiPlatform\Serializer;
use ReflectionException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class CommandProcessor implements ProcessorInterface
{
    /**
     * @param CommandBusInterface $commandBus
     * @param Serializer $apiPlatformSerializer
     */
    public function __construct(
        private readonly CommandBusInterface $commandBus,
        private readonly Serializer $apiPlatformSerializer,
    ) {
    }

    /**
     * @param $data
     * @param Operation $operation
     * @param array $uriVariables
     * @param array $context
     *
     * @return void
     *
     * @throws NoExtraPropertiesFoundException
     * @throws ExceptionInterface|ReflectionException
     */
    public function process($data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $commandClass = $operation->getExtraProperties()['command'] ?? null;

        if (null === $commandClass) {
            throw new NoExtraPropertiesFoundException('Extra property "command" not found');
        }

        $command = $this->apiPlatformSerializer->denormalize($data, $commandClass);

        $this->commandBus->handle($command);
    }
}
