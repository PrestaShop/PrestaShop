<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\PrestaShopBundle\Entity\Repository;

use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShopBundle\Entity\Repository\LangRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LangRepositoryTest extends KernelTestCase
{
    const SERVICE_NAME = 'prestashop.core.admin.lang.repository';

    public function setUp()
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testInterface()
    {
        /** @var LanguageRepositoryInterface $languageRepository */
        $languageRepository = self::$kernel->getContainer()->get(static::SERVICE_NAME);
        $this->assertNotNull($languageRepository);
        $this->assertInstanceOf(LanguageRepositoryInterface::class, $languageRepository);
    }

    public function testGetByLocale()
    {
        /** @var LanguageRepositoryInterface $languageRepository */
        $languageRepository = self::$kernel->getContainer()->get(static::SERVICE_NAME);
        $availableLocales = ['en-US'];
        foreach ($availableLocales as $availableLocale) {
            $language = $languageRepository->getOneByLocale($availableLocale);
            $this->assertNotNull($language);
            $this->assertInstanceOf(LanguageInterface::class, $language);
        }

        $notAvailableLocales = ['en-UK', 'en', 'fr'];
        foreach ($notAvailableLocales as $notAvailableLocale) {
            $language = $languageRepository->getOneByLocale($notAvailableLocale);
            $this->assertNull($language);
        }
    }

    public function testGetByIsoCode()
    {
        /** @var LanguageRepositoryInterface $languageRepository */
        $languageRepository = self::$kernel->getContainer()->get(static::SERVICE_NAME);
        $availableLocales = ['en'];
        foreach ($availableLocales as $availableLocale) {
            $language = $languageRepository->getOneByIsoCode($availableLocale);
            $this->assertNotNull($language);
            $this->assertInstanceOf(LanguageInterface::class, $language);
        }

        $notAvailableLocales = ['en-UK', 'fr'];
        foreach ($notAvailableLocales as $notAvailableLocale) {
            $language = $languageRepository->getOneByIsoCode($notAvailableLocale);
            $this->assertNull($language);
        }
    }

    public function testGetLocaleByIsoCode()
    {
        /** @var LangRepository $languageRepository */
        $languageRepository = self::$kernel->getContainer()->get(static::SERVICE_NAME);
        $locale = $languageRepository->getLocaleByIsoCode('en');
        $this->assertEquals('en-US', $locale);
    }

    protected function tearDown()
    {
        self::$kernel->shutdown();
    }
}
