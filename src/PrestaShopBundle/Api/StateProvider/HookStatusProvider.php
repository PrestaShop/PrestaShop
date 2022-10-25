<?php

namespace PrestaShopBundle\Api\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Hook\Query\GetHookStatus;
use PrestaShop\PrestaShop\Core\Domain\Hook\ValueObject\HookId;

final class HookStatusProvider implements ProviderInterface
{

    private $queryBus;

    public function __construct(CommandBusInterface $queryBus)
    {
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritDoc}
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = [])
    {
        return $this->queryBus->handle(new GetHookStatus($uriVariables['id']));
    }
}
