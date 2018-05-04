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

namespace Tests\Unit\Core\Localization\CLDR\DataLayer;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Localization\CLDR\LocaleData as CldrLocaleData;
use PrestaShop\PrestaShop\Core\Localization\CLDR\ReaderInterface;
use PrestaShop\PrestaShop\Core\Localization\CLDR\DataLayer\LocaleReference as CldrLocaleReferenceDataLayer;

class LocaleReferenceTest extends TestCase
{
    /**
     * The tested data layer
     *
     * @var CldrLocaleReferenceDataLayer
     */
    protected $layer;

    protected $stubLocaleData;

    protected function setUp()
    {
        $this->stubLocaleData      = new CldrLocaleData();
        $this->stubLocaleData->foo = ['bar', 'baz'];

        $fakeReader = $this->getMockBuilder(ReaderInterface::class)
            ->setMethods(['readLocaleData'])
            ->getMock();
        $fakeReader->method('readLocaleData')
            ->willReturnMap([
                ['fr-FR', $this->stubLocaleData],
                ['un-KNOWN', null], // Simulates an unknown locale
            ]);

        /** @var ReaderInterface $fakeReader */
        $this->layer = new CldrLocaleReferenceDataLayer($fakeReader);
    }

    /**
     * Given a valid CldrLocaleReferenceDataLayer object
     * When asking it for a given locale's data
     * Then the expected CLDR LocaleData object should be retrieved, of null if locale code is unknown.
     */
    public function testRead()
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $cldrLocaleData = $this->layer->read('fr-FR');
        /** @noinspection end */

        $this->assertInstanceOf(
            CldrLocaleData::class,
            $cldrLocaleData
        );

        $this->assertSame(
            ['bar', 'baz'],
            $cldrLocaleData->foo
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $cldrLocaleData = $this->layer->read('un-KNOWN');
        /** @noinspection end */

        $this->assertNull($cldrLocaleData);
    }
}
