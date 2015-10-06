<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

namespace PrestaShop\PrestaShop\Tests\Integration\Classes;

use PrestaShop\PrestaShop\Tests\TestCase\IntegrationTestCase;
use Context;
use Cookie;
use Configuration;

class CookieCoreTest extends IntegrationTestCase
{
    protected function setUp()
    {
        $this->context = Context::getContext();
        $this->domain = Configuration::get('PS_SHOP_DOMAIN');
    }
    public function isDomainProvider()
    {
        return array(
            array(null, null, '/server/'),
            array('www.prestashop.com', null, 'www.prestashop.com'),
            array('www.prestashop.com', array('www2.prestashop.com'), '.prestashop.com'),
            array('domain.info', null, 'domain.info'),
            array('shop.palermo.it', null, 'shop.palermo.it'),
            array('www.site.ingatlan.hu', array('www2.site.ingatlan.hu'), '.site.ingatlan.hu'),
            array('domain.doesnotexist.it', array('domain2.doesnotexist.it'), '.doesnotexist.it'),
            array('site.co.uk', null, 'site.co.uk'),
            array('subdomain1.site.com', array('subdomain.site2.com'), 'subdomain1.site.com'),
        );
    }
    /**
     * @dataProvider isDomainProvider
     */
    public function testgetDomain($input, $shared_urls, $output)
    {
        $output = str_replace('/server/', $this->domain, $output);

        $return = $this->context->cookie->getDomain($shared_urls, $input);
        $this->assertEquals($output, $return, 'getDomain failed for data input : '.$input.'; Expected : '.$output.'; Returns : '.$return);
    }
}
