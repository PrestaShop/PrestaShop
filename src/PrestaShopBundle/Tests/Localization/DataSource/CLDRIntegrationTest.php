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

namespace PrestaShopBundle\Tests\Localization\Repository;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Localization\CLDR\DataReader;
use PrestaShopBundle\Localization\DataSource\CLDR as CLDRLocaleRepository;

class CLDRIntegrationTest extends TestCase
{
    /**
     * CLDR locale repository
     *
     * @var CLDRLocaleRepository
     */
    protected $repo;

    public function setUp()
    {
        $this->repo = new CLDRLocaleRepository(new DataReader());
    }

    public function testGetById()
    {
        $this->assertNull($this->repo->getLocaleById(1));
    }

    /**
     * Given an invalid locale id (invalid type)
     * When requesting a locale with this id
     * Then an InvalidArgumentException should be raised
     *
     * @expectedException InvalidArgumentException
     */
    public function testGetByIdWithWrongParameter()
    {
        $this->repo->getLocaleById('foobar');
    }

    /**
     * Given a valid locale code
     * When requesting the locale having this code
     * Then the expected locale data should be returned
     *
     * @param $localeCode
     * @param $expectedData
     *
     * @dataProvider provideValidLocaleData
     */
    public function testGetByLocaleCode($localeCode, $expectedData)
    {
        $localeData = $this->repo->getLocaleByCode($localeCode);

        foreach ($expectedData as $property => $value) {
            // TODO : should be LocaleData instances, not plain array data
            // TODO object comparison
            $this->assertSame($value, $localeData[$property]);
        }
    }

    public function provideValidLocaleData()
    {
        return array(
            'fr-FR - EUR' => array(
                'localeCode'   => 'fr-FR',
                'expectedData' => array(
                    // TODO
                ),
            ),
        );
    }
}
