<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShopBundle\Translation\TranslatorComponent;

class CheckoutProcessProviderResolverCore
{
    public const PROVIDER_MODULE_CONFIG_KEY = 'PS_CHECKOUT_PROCESS_PROVIDER_MODULE';

    /**
     * Returns the checkout process provided by the configured checkout module.
     *
     * Returns null when the checkout must fall back to the native process.
     *
     * @param CheckoutSession $session Current checkout session
     * @param TranslatorComponent $translator Translator used by the provider
     *
     * @return CheckoutProcess|null Module checkout process, or null to use the native checkout
     */
    public function resolve(CheckoutSession $session, TranslatorComponent $translator): ?CheckoutProcess
    {
        $providerModuleName = $this->getProviderModuleName();
        if (null === $providerModuleName) {
            return null;
        }

        $providerModuleId = $this->getProviderModuleId($providerModuleName);
        if (null === $providerModuleId) {
            return null;
        }

        $hookOutput = Hook::exec('actionCheckoutBuildProcess', [
            'checkoutSession' => $session,
            'translator' => $translator,
        ], $providerModuleId, true);

        if (!is_array($hookOutput) || !array_key_exists($providerModuleName, $hookOutput)) {
            return null;
        }

        $providerOutput = $hookOutput[$providerModuleName];
        if ($providerOutput instanceof CheckoutProcess) {
            return $providerOutput;
        }

        return null;
    }

    /**
     * @return string|null
     */
    protected function getProviderModuleName(): ?string
    {
        $providerModuleName = strtolower(trim((string) Configuration::get(self::PROVIDER_MODULE_CONFIG_KEY)));

        return '' !== $providerModuleName
            ? $providerModuleName
            : null;
    }

    /**
     * @param string $providerModuleName
     *
     * @return int|null
     */
    protected function getProviderModuleId(string $providerModuleName): ?int
    {
        $providerModuleId = (int) Module::getModuleIdByName($providerModuleName);
        if ($providerModuleId <= 0) {
            return null;
        }

        $providerModule = Module::getInstanceByName($providerModuleName);
        if (!$providerModule instanceof Module || !$providerModule->isEnabledForShopContext()) {
            return null;
        }

        return $providerModuleId;
    }
}
