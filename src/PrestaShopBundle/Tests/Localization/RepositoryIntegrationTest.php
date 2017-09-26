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

namespace PrestaShopBundle\Tests\Localization;

use PrestaShopBundle\Localization\Locale;
use PrestaShopBundle\Localization\Repository as LocaleRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RepositoryIntegrationTest extends KernelTestCase
{
    /** @var LocaleRepository */
    protected $repository;

    public function setUp()
    {
        $kernel = $this->createKernel();
        $kernel->boot();
        $this->repository = $kernel->getContainer()->get('prestashop.cldr.locale.repository.reference');
    }

    /**
     * Given a valid locale code
     * When requesting a Locale to the locale repository with this code
     * It should return a valid Locale instance with expected data
     *
     * @param $localeCode
     * @param $expectedLocaleData
     *
     * @dataProvider provideValidLocaleData
     */
    public function testItReturnsLocaleWithCode($localeCode, $expectedLocaleData)
    {
        /** @var Locale $locale */
        $locale = $this->repository->getLocaleByCode($localeCode);
        $this->assertSame($expectedLocaleData['localeCode'], $locale->getLocaleCode());
    }

    public function provideValidLocaleData()
    {
        return array(
            'ar-IL' => array(
                'localeCode'   => 'ar-IL',
                'expectedData' => [
                    'localeCode'   => 'ar-IL',
                ],
            ),
            'bn-IN' => array(
                'localeCode'   => 'bn-IN',
                'expectedData' => [
                    'localeCode'   => 'bn-IN',
                ],
            ),
            'de-CH' => array(
                'localeCode'   => 'de-CH',
                'expectedData' => [
                    'localeCode'   => 'de-CH',
                ],
            ),
            'en-US' => array(
                'localeCode'   => 'en-US',
                'expectedData' => [
                    'localeCode'   => 'en-US',
                ],
            ),
            'es-AR' => array(
                'localeCode'   => 'es-AR',
                'expectedData' => [
                    'localeCode'   => 'es-AR',
                ],
            ),
            'fr-FR' => array(
                'localeCode'   => 'fr-FR',
                'expectedData' => [
                    'localeCode'   => 'fr-FR',
                ],
            ),
        );
    }
}
