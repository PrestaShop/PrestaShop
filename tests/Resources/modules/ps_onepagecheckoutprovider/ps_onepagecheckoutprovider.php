<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Adapter\Order\Checkout\CheckoutProcessProviderInterface;
use PrestaShopBundle\Translation\TranslatorComponent;

class PsOnePageCheckoutTestProvider implements CheckoutProcessProviderInterface
{
    public function __construct(
        private readonly Context $context,
        private readonly bool $enabled
    ) {
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function buildCheckoutProcess(
        $session,
        TranslatorComponent $translator
    ): CheckoutProcess {
        return new CheckoutProcess($this->context, $session);
    }
}

class Ps_OnePageCheckoutProvider extends Module
{
    public const OUTPUT_MODE_CONFIG_KEY = 'CHECKOUT_PROCESS_PROVIDER_TEST_OUTPUT';

    public function __construct()
    {
        $this->name = 'ps_onepagecheckoutprovider';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = 'Checkout process provider test module';
        $this->description = 'Provides a checkout process for integration tests.';
    }

    public function install()
    {
        return parent::install() && $this->registerHook('actionCheckoutBuildProcess');
    }

    public function uninstall()
    {
        Configuration::deleteByName(static::OUTPUT_MODE_CONFIG_KEY);

        return parent::uninstall();
    }

    public function hookActionCheckoutBuildProcess(array $params)
    {
        $mode = (string) Configuration::get(static::OUTPUT_MODE_CONFIG_KEY);

        if ($mode === 'invalid') {
            return 'invalid';
        }

        return new PsOnePageCheckoutTestProvider($this->context, $mode !== 'disabled');
    }
}
