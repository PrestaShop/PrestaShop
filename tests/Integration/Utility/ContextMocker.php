<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Utility;

use Address;
use Cache;
use Carrier;
use Cart;
use CartRule;
use Configuration;
use Context;
use Cookie;
use Country;
use Currency;
use Customer;
use Employee;
use Language;
use Link;
use Module;
use ObjectModel;
use Pack;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use Product;
use Shop;
use SpecificPrice;
use Tools;

/**
 * This helper class provides methods to initialize context for front controller tests
 *
 * USAGE :
 *
 * public function setUp(): void
 * {
 *     parent::setUp();
 *     $this->contextMocker = (new ContextMocker())->mockContext();
 * }
 *
 *
 * public function tearDown(): void
 * {
 *     parent::tearDown();
 *     $this->contextMocker->resetContext();
 * }
 */
class ContextMocker
{
    /**
     * @var Context
     */
    private $backupContext;

    /**
     * @var Context|null
     */
    private $mockedContext;

    /**
     * properly mock global context object with required properties
     * with this mock front controllers are able to be tested
     *
     * @return static
     */
    public function mockContext()
    {
        global $smarty;

        // need to reset loooot of things
        Product::flushPriceCache();
        SpecificPrice::flushCache();
        Configuration::resetStaticCache();
        Configuration::loadConfiguration();
        Cache::clear();
        Cart::resetStaticCache();
        Carrier::resetStaticCache();
        CartRule::resetStaticCache();
        Currency::resetStaticCache();
        Shop::resetContext();
        SymfonyContainer::resetStaticCache();
        Pack::resetStaticCache();
        Tools::$round_mode = null;
        Customer::resetAddressCache();
        Address::resetStaticCache();
        ObjectModel::resetStaticCache();
        Tools::resetStaticCache();

        Cache::clean('*');

        $this->backupContext();
        $context = clone $this->backupContext;
        Context::setInstanceForTesting($context);
        LegacyContext::setInstanceForTesting($context);
        Module::setContextInstanceForTesting($context);
        $context->shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
        Shop::setContext(Shop::CONTEXT_SHOP, (int) Context::getContext()->shop->id);
        $context->customer = new Customer();
        $context->cookie = new Cookie('mycookie');
        $context->country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        // Use super admin employee by default
        $context->employee = new Employee(1);
        $context->employee->id_lang = $context->language->id;
        $context->currency = Currency::getDefaultCurrency();
        $protocol_link = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED'))
            ? 'https://' : 'http://';
        $protocol_content = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED'))
            ? 'https://' : 'http://';
        $context->link = new Link($protocol_link, $protocol_content);
        $context->cart = new Cart();
        $context->smarty = $smarty;

        $this->mockedContext = $context;

        return $this;
    }

    /**
     * Backup current context
     */
    public function backupContext(): void
    {
        $this->backupContext = Context::getContext();
    }

    /**
     * Restore previous context to avoid modifying global properties through tests
     */
    public function resetContext()
    {
        Context::setInstanceForTesting($this->backupContext);
        LegacyContext::setInstanceForTesting($this->backupContext);
        // If a shop context was previously reset it, if not rest shop context
        if (Context::getContext()->shop && Context::getContext()->shop->id) {
            Shop::setContext(Shop::CONTEXT_SHOP, (int) Context::getContext()->shop->id);
        } else {
            Shop::resetContext();
        }
        Module::setContextInstanceForTesting($this->backupContext);
    }

    public function getMockedContext(): ?Context
    {
        return $this->mockedContext;
    }

    public function getBackupContext(): ?Context
    {
        return $this->backupContext;
    }

    public function getContext(): Context
    {
        if ($this->mockedContext) {
            return $this->mockedContext;
        }

        return $this->backupContext;
    }
}
