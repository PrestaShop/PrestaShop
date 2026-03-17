<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Adapter\LegacyLogger;
use PrestaShopBundle\Translation\TranslatorComponent;
use Psr\Log\LoggerInterface;

class CheckoutProcessProviderResolverCore
{
    public const PROVIDER_MODULE_CONFIG_KEY = 'PS_CHECKOUT_PROCESS_PROVIDER_MODULE';

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct()
    {
        $this->logger = new LegacyLogger();
    }

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

        if (!is_array($hookOutput) || empty($hookOutput)) {
            $this->logProviderWarning(
                sprintf(
                    'Configured module "%s" is not registered on hook actionCheckoutBuildProcess.',
                    $providerModuleName
                )
            );

            return null;
        }

        if (!array_key_exists($providerModuleName, $hookOutput)) {
            $this->logProviderWarning(
                sprintf(
                    'Configured module "%s" is not the hook output key on actionCheckoutBuildProcess. Returned keys: [%s].',
                    $providerModuleName,
                    implode(', ', array_keys($hookOutput))
                )
            );

            return null;
        }

        $providerOutput = $hookOutput[$providerModuleName];

        if ($providerOutput instanceof CheckoutProcess) {
            return $providerOutput;
        }

        if (null !== $providerOutput) {
            $this->logProviderWarning(
                sprintf(
                    'Configured module "%s" returned "%s" on actionCheckoutBuildProcess. Expected CheckoutProcess.',
                    $providerModuleName,
                    is_object($providerOutput) ? $providerOutput::class : gettype($providerOutput)
                )
            );
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
            $this->logProviderWarning(
                sprintf(
                    'Configured module "%s" does not exist.',
                    $providerModuleName
                )
            );

            return null;
        }

        $providerModule = Module::getInstanceByName($providerModuleName);
        if (!$providerModule instanceof Module) {
            $this->logProviderWarning(
                sprintf(
                    'Configured module "%s" cannot be loaded.',
                    $providerModuleName
                )
            );

            return null;
        }

        if (!$providerModule->isEnabledForShopContext()) {
            $this->logProviderWarning(
                sprintf(
                    'Configured module "%s" is disabled for current shop context.',
                    $providerModuleName
                )
            );

            return null;
        }

        return $providerModuleId;
    }

    /**
     * @param string $message
     */
    protected function logProviderWarning(string $message): void
    {
        $this->logger->warning('Checkout process provider: ' . $message);
    }
}
