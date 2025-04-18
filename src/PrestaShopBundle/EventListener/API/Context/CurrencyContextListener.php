<?php

namespace PrestaShopBundle\EventListener\API\Context;

use PrestaShop\PrestaShop\Core\Context\CurrencyContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Listener dedicated to set up Currency context for the Back-Office/Admin application.
 */
class CurrencyContextListener
{
    public function __construct(
        private readonly CurrencyContextBuilder $currencyContextBuilder,
        private readonly ShopConfigurationInterface $configuration,
        private readonly ShopContext $shopContext
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $defaultCurrencyId = $this->configuration->get('PS_CURRENCY_DEFAULT', null, ShopConstraint::shop($this->shopContext->getId()));

        $this->currencyContextBuilder->setCurrencyId($defaultCurrencyId);

        $currencyId = $event->getRequest()->get('currencyId', $defaultCurrencyId);
        if ($currencyId) {
            $this->currencyContextBuilder->setCurrencyId((int) $currencyId);
        } else {
            $this->currencyContextBuilder->setCurrencyId($defaultCurrencyId);
        }
    }
}
