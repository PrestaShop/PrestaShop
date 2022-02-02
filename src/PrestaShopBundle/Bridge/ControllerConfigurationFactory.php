<?php

namespace PrestaShopBundle\Bridge;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class ControllerConfigurationFactory
{
    /**
     * @var LegacyContext
     */
    private $legacyContext;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(
        LegacyContext $legacyContext,
        TokenStorage $tokenStorage
    ) {
        $this->legacyContext = $legacyContext;
        $this->tokenStorage = $tokenStorage;
    }

    public function create(array $configuration = []): ControllerConfiguration
    {
        $configuratorController = new ControllerConfiguration();
        $configuratorController->context = $this->legacyContext->getContext();

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $configuration = $resolver->resolve($configuration);

        $configuratorController->id = $configuration['id'];
        $configuratorController->controllerName = $configuration['controllerName'];
        $configuratorController->controllerNameLegacy = $configuration['controllerNameLegacy'];
        $configuratorController->positionIdentifier = $configuration['positionIdentifier'];
        $configuratorController->table = $configuration['table'];
        $configuratorController->user = $this->getUser();
        $configuratorController->link = $this->legacyContext->getContext()->link;
        $configuratorController->cookie = $this->legacyContext->getContext()->cookie;
        $configuratorController->language = $this->legacyContext->getContext()->language;
        $configuratorController->shop = $this->legacyContext->getContext()->shop;
        $configuratorController->country = $this->legacyContext->getContext()->country;

        return $configuratorController;
    }

    private function getUser()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        if (!\is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }

    private function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'id',
            'controllerName',
            'controllerNameLegacy',
            'positionIdentifier',
            'table',
        ]);
    }
}
