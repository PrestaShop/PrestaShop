<?php
/**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit;

use Cache;
use Carrier;
use Cart;
use CartRule;
use Configuration;
use Context;
use Currency;
use Language;
use Link;
use Phake;
use Product;
use Shop;
use Smarty;
use SpecificPrice;
use Tools;
use Pack;

/**
 * This helper class provides methods to initialize context for front controller tests
 *
 * USAGE :
 *
 * public function setUp()
 * {
 *     parent::setUp();
 *     $this->contextMocker = (new ContextMocker())->mockContext();
 * }
 *
 *
 * public function tearDown()
 * {
 *     parent::tearDown();
 *     $this->contextMocker->resetContext();
 * }
 */
class ContextMocker
{

    /**
     * @var \Context
     */
    private $contextBackup;

    /**
     * properly mock global context object with required properties
     * with this mock front controllers are able to be tested
     *
     * @return static
     */
    public function mockContext()
    {
        // need to reset loooot of things
        Product::flushPriceCache();
        SpecificPrice::flushCache();
        Configuration::clearConfigurationCacheForTesting();
        Configuration::loadConfiguration();
        Cache::clear();
        Cart::resetStaticCache();
        Carrier::resetStaticCache();
        CartRule::resetStaticCache();
        Currency::resetStaticCache();
        Shop::resetContext();
        Pack::resetStaticCache();
        Tools::$round_mode = null;

        $this->contextBackup = Context::getContext();
        $context             = clone($this->contextBackup);
        Context::setInstanceForTesting($context);
        $context->shop = new Shop((int) Configuration::get('PS_SHOP_DEFAULT'));
        Shop::setContext(Shop::CONTEXT_SHOP, (int) Context::getContext()->shop->id);
        $context->customer = Phake::mock('Customer');
        $context->cookie   = Phake::mock('Cookie');
        $context->country  = Phake::mock('Country');
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $protocol_link     = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED'))
            ? 'https://' : 'http://';
        $protocol_content  = (Tools::usingSecureMode() && Configuration::get('PS_SSL_ENABLED'))
            ? 'https://' : 'http://';
        $context->link     = new Link($protocol_link, $protocol_content);
        $context->currency = new Currency(1, 1, 1);
        $context->smarty   = new Smarty();

        return $this;
    }

    /**
     * restore previous context to avoid modifying global properties through tests
     */
    public function resetContext()
    {
        Context::setInstanceForTesting($this->contextBackup);
    }
}
