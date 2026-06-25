<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

use PrestaShop\PrestaShop\Adapter\Order\Checkout\CheckoutProcessProviderInterface;
use PrestaShopBundle\Translation\TranslatorComponent;

class PsConflictingCheckoutTestProvider implements CheckoutProcessProviderInterface
{
    public function __construct(private readonly Context $context)
    {
    }

    public function isEnabled(): bool
    {
        return true;
    }

    public function buildCheckoutProcess(
        $session,
        TranslatorComponent $translator
    ): CheckoutProcess {
        return new CheckoutProcess($this->context, $session);
    }
}

class Ps_ConflictingCheckoutProvider extends Module
{
    public function __construct()
    {
        $this->name = 'ps_conflictingcheckoutprovider';
        $this->version = '1.0.0';
        $this->author = 'PrestaShop';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = 'Conflicting checkout process provider test module';
        $this->description = 'Provides a second checkout process provider for integration tests.';
    }

    public function install()
    {
        return parent::install() && $this->registerHook('actionCheckoutBuildProcess');
    }

    public function hookActionCheckoutBuildProcess(array $params)
    {
        return new PsConflictingCheckoutTestProvider($this->context);
    }
}
