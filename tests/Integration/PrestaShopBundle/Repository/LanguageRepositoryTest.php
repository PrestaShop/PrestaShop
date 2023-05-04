<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\PrestaShopBundle\Repository;

use PrestaShop\PrestaShop\Core\Model\Exception\LanguageNotFoundException;
use PrestaShopBundle\Repository\LanguageRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LanguageRepositoryTest extends KernelTestCase
{
    /**
     * @var LanguageRepository
     */
    private $languageRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->languageRepository = self::$kernel->getContainer()->get(LanguageRepository::class);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->languageRepository);
    }

    public function testConstruct(): void
    {
        $this->assertInstanceOf(LanguageRepository::class, $this->languageRepository);
    }

    public function testGetLanguage(): void
    {
        $language = $this->languageRepository->getLanguage(1);
        $this->assertNotNull($language);
        $this->assertEquals(1, $language->getId());
    }

    public function testGetLanguageByIsoCode(): void
    {
        $language = $this->languageRepository->getLanguageByIsoCode('en');
        $this->assertNotNull($language);
        $this->assertEquals('en', $language->getIsoCode());
    }

    public function testGetLanguageByLocale(): void
    {
        $language = $this->languageRepository->getLanguageByLocale('en-US');
        $this->assertNotNull($language);
        $this->assertEquals('en-US', $language->getLocale());
    }

    public function testFindAll(): void
    {
        $languages = $this->languageRepository->findAll();
        $this->assertNotEmpty($languages);
    }

    public function testFindBy(): void
    {
        $filters = ['isoCode' => 'en'];
        $languages = $this->languageRepository->findBy($filters);
        $this->assertNotEmpty($languages);
        $this->assertEquals('en', $languages[0]->getIsoCode());
    }

    public function testGetLanguageNotFound(): void
    {
        $this->expectException(LanguageNotFoundException::class);
        $this->languageRepository->getLanguage(9999);
    }

    public function testGetLanguageByIsoCodeNotFound(): void
    {
        $this->expectException(LanguageNotFoundException::class);
        $this->languageRepository->getLanguageByIsoCode('does-not-exist');
    }

    public function testGetLanguageByLocaleNotFound(): void
    {
        $this->expectException(LanguageNotFoundException::class);
        $this->languageRepository->getLanguageByLocale('does-not-exist');
    }

    public function testFindByNotFound(): void
    {
        $this->expectException(LanguageNotFoundException::class);
        $filters = ['isoCode' => 'does-not-exist'];
        $this->languageRepository->findBy($filters);
    }
}
