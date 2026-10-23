<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\EventListener\Console;

use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\CurrencyContextBuilder;

/**
 * A console command has no request, so the listeners that set the context currency for the back office
 * never run for it. Set the default currency, as the back office does, before the command starts: legacy
 * code it calls rounds with the context currency's precision, for instance when an order status change
 * creates an invoice.
 */
final class CurrencyContextCommandListener
{
    public function __construct(
        private readonly CurrencyContextBuilder $currencyContextBuilder,
        private readonly ConfigurationInterface $configuration,
        private readonly LegacyContext $legacyContext,
    ) {
    }

    public function onConsoleCommand(): void
    {
        $context = $this->legacyContext->getContext();
        // WHY: bin/console still runs on a shop that is not installed (cache:clear, list), and there is
        // no shop, configuration or currency to read then. A currency already set is kept.
        if (empty($context->shop->id) || null !== $context->currency) {
            return;
        }

        $currencyId = (int) $this->configuration->get('PS_CURRENCY_DEFAULT');
        if ($currencyId <= 0) {
            return;
        }

        $this->currencyContextBuilder->setCurrencyId($currencyId);
        $this->currencyContextBuilder->buildLegacyContext();
    }
}
