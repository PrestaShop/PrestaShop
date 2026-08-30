<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order\Checkout;

use CheckoutProcess;
use CheckoutSession;
use Hook;
use PrestaShopBundle\Translation\TranslatorComponent;

/**
 * Resolves the checkout process provided by modules through the checkout hook.
 */
class CheckoutProcessProviderResolver
{
    /**
     * Returns the checkout process provided by modules when exactly one valid provider is available,
     * or null to keep the native checkout.
     *
     * @param CheckoutSession $session
     * @param TranslatorComponent $translator
     *
     * @return CheckoutProcess|null
     */
    public function resolve(CheckoutSession $session, TranslatorComponent $translator): ?CheckoutProcess
    {
        $providers = $this->getValidProviders();
        $providersCount = count($providers);

        if (0 === $providersCount) {
            return null;
        }

        if ($providersCount > 1) {
            return null;
        }

        /** @var CheckoutProcessProviderInterface $provider */
        $provider = reset($providers);
        $checkoutProcess = $provider->buildCheckoutProcess($session, $translator);

        return $checkoutProcess instanceof CheckoutProcess ? $checkoutProcess : null;
    }

    /**
     * @return array<string, CheckoutProcessProviderInterface>
     */
    protected function getValidProviders(): array
    {
        $providers = [];
        $hookOutput = Hook::exec('actionCheckoutBuildProcess', [], null, true);

        if (!is_array($hookOutput)) {
            return $providers;
        }

        foreach ($hookOutput as $moduleName => $provider) {
            if (!$provider instanceof CheckoutProcessProviderInterface || !$provider->isEnabled()) {
                continue;
            }

            $providers[$moduleName] = $provider;
        }

        return $providers;
    }
}
