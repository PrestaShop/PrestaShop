<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\API\Context;

use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Configuration\ShopConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Listener dedicated to set up Language context for the Back-Office/Admin application.
 */
class LanguageContextListener
{
    public function __construct(
        private readonly LanguageContextBuilder $languageContextBuilder,
        private readonly ShopConfigurationInterface $configuration,
        private readonly ShopContext $shopContext
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $defaultLanguageId = (int) $this->configuration->get('PS_LANG_DEFAULT', null, ShopConstraint::shop($this->shopContext->getId()));
        $this->languageContextBuilder->setDefaultLanguageId($defaultLanguageId);

        $langId = $event->getRequest()->get('langId');
        if ($langId) {
            $this->languageContextBuilder->setLanguageId((int) $langId);
        } else {
            $this->languageContextBuilder->setLanguageId($defaultLanguageId);
        }
    }
}
