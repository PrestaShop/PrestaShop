<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Cldr;

use Tests\TestCase\UnitTestCase;
use ICanBoogie\CLDR\FileProvider;
use ICanBoogie\CLDR\Repository;
use ICanBoogie\CLDR\RunTimeProvider;
use ICanBoogie\CLDR\WebProvider;
use ICanBoogie\CLDR\Currency;

class RepositoryTest extends UnitTestCase
{
    private $locale;
    private $region;

    public function setUp()
    {
        $this->cldrCacheFolder = _PS_CACHE_DIR_.'cldr-test';
        $this->locale = 'fr';
        $this->region = 'FR';

        $provider = new RunTimeProvider(new FileProvider(new WebProvider('http://i18n.prestashop.com/cldr/json-full/'), _PS_CACHE_DIR_.'cldr-test'));

        $this->repository = new Repository($provider);
        $this->localeRepository = $this->repository->locales[$this->locale];
    }

    public static function setUpBeforeClass()
    {
        \Tools::deleteDirectory(_PS_CACHE_DIR_.'cldr-test');
    }

    public static function tearDownAfterClass()
    {
        \Tools::deleteDirectory(_PS_CACHE_DIR_.'cldr-test');
    }

    public function testCacheFolderIsCreated()
    {
        $this->assertFalse(file_exists($this->cldrCacheFolder));

        mkdir($this->cldrCacheFolder, 0777, true);

        $this->assertTrue(file_exists($this->cldrCacheFolder));
    }

    public function testSetLocale()
    {
        $this->locale = 'fr';
        $this->localeRepository = $this->repository->locales[$this->locale];

        $this->assertEquals($this->locale, 'fr');
        $this->assertEquals($this->localeRepository->__get('code'), 'fr');
    }

    public function testSetRegion()
    {
        $this->region = 'FR';
        $this->assertEquals($this->region, 'FR');
    }

    public function testGetCurrencyIsoCodeNum()
    {
        $code = 'EUR';
        $currencies = $this->repository->supplemental['codeMappings'];

        $this->assertEquals($currencies[$code]['_numeric'], '978');
    }

    public function testGetCurrencyWithoutCode()
    {
        $territory = $this->repository->territories[$this->region];

        $this->assertEquals($territory->code, 'FR');

        $code = (string)$territory->currency;

        $this->assertEquals($code, 'EUR');

        $currency = new Currency($this->repository, $code);
        $localized_currency = $currency->localize($this->locale);

        $this->assertEquals($localized_currency->name, 'euro');
    }

    public function testGetCurrencyWithCode()
    {
        $code = 'EUR';

        $currency = new Currency($this->repository, $code);
        $localized_currency = $currency->localize($this->locale);

        $this->assertEquals($localized_currency->name, 'euro');
    }

    public function testGetAllCurrencies()
    {
        $currencies = $this->repository->supplemental['codeMappings'];

        $this->assertCount(486, $currencies);

        $datas = array();
        foreach ($currencies as $k => $v) {
            if (strlen($k)!==3) {
                continue;
            }
            $datas[] = $k;
        }

        $this->assertCount(177, $datas);
    }
}
