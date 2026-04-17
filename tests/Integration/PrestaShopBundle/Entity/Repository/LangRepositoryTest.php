<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\PrestaShopBundle\Entity\Repository;

use PrestaShop\PrestaShop\Core\Language\LanguageInterface;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShopBundle\Entity\Repository\LangRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LangRepositoryTest extends KernelTestCase
{
    private const SERVICE_NAME = 'prestashop.core.admin.lang.repository';

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    public function testInterface(): void
    {
        /** @var LanguageRepositoryInterface $languageRepository */
        $languageRepository = self::$kernel->getContainer()->get(self::SERVICE_NAME);
        $this->assertNotNull($languageRepository);
        $this->assertInstanceOf(LanguageRepositoryInterface::class, $languageRepository);
    }

    public function testGetByLocale(): void
    {
        /** @var LanguageRepositoryInterface $languageRepository */
        $languageRepository = self::$kernel->getContainer()->get(self::SERVICE_NAME);
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

    public function testGetByIsoCode(): void
    {
        /** @var LanguageRepositoryInterface $languageRepository */
        $languageRepository = self::$kernel->getContainer()->get(self::SERVICE_NAME);
        $availableLocales = ['en'];
        foreach ($availableLocales as $availableLocale) {
            $language = $languageRepository->getOneByIsoCode($availableLocale);
            $this->assertNotNull($language);
            $this->assertInstanceOf(LanguageInterface::class, $language);
        }

        $notAvailableLocales = ['en-UK', 'jp'];
        foreach ($notAvailableLocales as $notAvailableLocale) {
            $language = $languageRepository->getOneByIsoCode($notAvailableLocale);
            $this->assertNull($language);
        }
    }

    public function testGetLocaleByIsoCode(): void
    {
        /** @var LangRepository $languageRepository */
        $languageRepository = self::$kernel->getContainer()->get(self::SERVICE_NAME);
        $locale = $languageRepository->getLocaleByIsoCode('en');
        $this->assertEquals('en-US', $locale);
    }
}
