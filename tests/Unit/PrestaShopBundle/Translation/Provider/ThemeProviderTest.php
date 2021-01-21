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

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Translation\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Loader\DatabaseTranslationLoader;
use PrestaShopBundle\Translation\Provider\ThemeProvider;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\MessageCatalogue;

class ThemeProviderTest extends TestCase
{
    private const THEME_NAME = 'themeName';

    /**
     * @var string
     */
    private static $tempDir;
    /**
     * @var string
     */
    private static $tempThemesDir;

    private static $defaultTranslations = [
        'ShopDomain' => [
            'Some wording' => 'Some wording',
            'Some other wording' => 'Some other wording',
            'Some third wording' => 'Some third wording',
        ],
        'ModulesDomainShop' => [
            'Foo' => 'Foo',
            'Bar' => 'Bar',
        ],
        'AdminSomeDomain' => [
            'Something' => 'Special',
            'OtherCommon' => 'Thing',
        ],
    ];

    private static $fileTranslations = [
        'ShopDomain' => [
            'Some wording' => 'File wording',
            'Some other wording' => 'File other wording',
        ],
        'ModulesDomainShop' => [
            'Foo' => 'File Foo',
            'Bar' => 'File Bar',
        ],
        'AdminSomeDomain' => [
            'Something' => 'File Special',
            'OtherCommon' => 'File Thing',
        ],
    ];

    private static $dbTranslations = [
        [
            'lang' => 'en-US',
            'key' => 'Some wording',
            'translation' => 'DB wording',
            'domain' => 'ShopDomain',
            'theme' => null,
        ],
        [
            'lang' => 'en-US',
            'key' => 'Foo',
            'translation' => 'DB Foo',
            'domain' => 'ModulesDomainShop',
            'theme' => null,
        ],
        [
            'lang' => 'en-US',
            'key' => 'Bar',
            'translation' => 'DB Bar',
            'domain' => 'ModulesDomainShop',
            'theme' => null,
        ],
        [
            'lang' => 'en-US',
            'key' => 'Something',
            'translation' => 'DB Special',
            'domain' => 'AdminSomeDomain',
            'theme' => null,
        ],
        [
            'lang' => 'en-US',
            'key' => 'OtherCommon',
            'translation' => 'DB Thing',
            'domain' => 'AdminSomeDomain',
            'theme' => null,
        ],
    ];

    private static $emptyWordings = [
        'ShopDomain.en-US' => [
            'Some wording' => '',
            'Some other wording' => '',
            'Some third wording' => '',
        ],
        'ModulesDomainShop.en-US' => [
            'Foo' => '',
            'Bar' => '',
        ],
        'AdminSomeDomain.en-US' => [
            'Something' => '',
            'OtherCommon' => '',
        ],
    ];

    private static $fileWordings = [
        'AdminSomeDomain' => [
            'Something' => 'File Special',
            'OtherCommon' => 'File Thing',
        ],
        'ShopDomain' => [
            'Some wording' => 'File wording',
            'Some other wording' => 'File other wording',
        ],
        'ModulesDomainShop' => [
            'Foo' => 'File Foo',
            'Bar' => 'File Bar',
        ],
    ];

    private static $dbWordings = [
        'ShopDomain' => [
            'Some wording' => 'DB wording',
        ],
        'ModulesDomainShop' => [
            'Foo' => 'DB Foo',
            'Bar' => 'DB Bar',
        ],
    ];

    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseTranslationLoader;

    public function setUp()
    {
        self::$tempDir = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), 'ThemeProviderTest']);
        self::$tempThemesDir = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), 'ThemeProviderTest', 'themes']);

        // Default catalogue
        $catalogue = new MessageCatalogue(ThemeProvider::DEFAULT_LOCALE);
        foreach (self::$defaultTranslations as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        (new XliffFileDumper())->dump($catalogue, [
            'path' => self::$tempDir . DIRECTORY_SEPARATOR . ThemeProvider::DEFAULT_LOCALE,
        ]);
        (new XliffFileDumper())->dump($catalogue, [
            'path' => self::$tempDir . DIRECTORY_SEPARATOR . 'ab-AB',
        ]);

        // Xliff catalogue
        $catalogue = new MessageCatalogue(ThemeProvider::DEFAULT_LOCALE);
        foreach (self::$fileTranslations as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        (new XliffFileDumper())->dump($catalogue, [
            'path' => implode(DIRECTORY_SEPARATOR, [self::$tempThemesDir, self::THEME_NAME, 'translations', ThemeProvider::DEFAULT_LOCALE]),
        ]);

        // Database catalogue
        $this->databaseTranslationLoader = new MockDatabaseTranslationLoader(self::$dbTranslations);
    }

    public function testGetDefaultCatalogue()
    {
        $provider = new ThemeProvider(
            $this->databaseTranslationLoader,
            self::$tempDir
        );
        $provider->defaultTranslationDir = self::$tempDir;
        $provider->themeResourcesDirectory = self::$tempThemesDir;
        $provider->filesystem = new Filesystem();
        $provider->setThemeName(self::THEME_NAME);

        $catalogue = $provider->getDefaultCatalogue(false);

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'AdminSomeDomain',
            'ModulesDomainShop',
            'ShopDomain',
        ], $domains);

        $messages = $catalogue->all();
        foreach (self::$defaultTranslations as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }

    public function testGetEmptyDefaultCatalogue()
    {
        $provider = new ThemeProvider(
            $this->databaseTranslationLoader,
            self::$tempDir
        );
        $provider->defaultTranslationDir = self::$tempDir;
        $provider->themeResourcesDirectory = self::$tempThemesDir;
        $provider->filesystem = new Filesystem();
        $provider->setThemeName(self::THEME_NAME);

        $catalogue = $provider->getDefaultCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'AdminSomeDomain',
            'ModulesDomainShop',
            'ShopDomain',
        ], $domains);

        // catalogue won't be emptied if locale = DEFAULT_LOCALE
        $messages = $catalogue->all();
        foreach (self::$defaultTranslations as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }

        $provider->setLocale('ab-AB');
        $catalogue = $provider->getDefaultCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'AdminSomeDomain.en-US',
            'ModulesDomainShop.en-US',
            'ShopDomain.en-US',
        ], $domains);

        // catalogue will be emptied if locale != DEFAULT_LOCALE
        $messages = $catalogue->all();
        foreach (self::$emptyWordings as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }

    public function testGetXliffCatalogue()
    {
        $provider = new ThemeProvider(
            $this->databaseTranslationLoader,
            self::$tempDir
        );
        $provider->defaultTranslationDir = self::$tempDir;
        $provider->themeResourcesDirectory = self::$tempThemesDir;
        $provider->filesystem = new Filesystem();
        $provider->setThemeName(self::THEME_NAME);

        $catalogue = $provider->getXliffCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'AdminSomeDomain',
            'ModulesDomainShop',
            'ShopDomain',
        ], $domains);

        $messages = $catalogue->all();
        foreach (self::$fileWordings as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }

    public function testGetDatabaseCatalogue()
    {
        // Translations in DB don't have theme name
        $provider = new ThemeProvider(
            $this->databaseTranslationLoader,
            self::$tempDir
        );
        $provider->defaultTranslationDir = self::$tempDir;
        $provider->themeResourcesDirectory = self::$tempThemesDir;
        $provider->filesystem = new Filesystem();
        $provider->setThemeName(self::THEME_NAME);
        $catalogue = $provider->getDatabaseCatalogue();

        $domains = $catalogue->getDomains();
        $this->assertEmpty($domains);

        $messages = $catalogue->all();
        $this->assertEmpty($messages);

        // Translations in DB have theme name but no theme selected in provider
        $dbTranslations = self::$dbTranslations;
        foreach ($dbTranslations as &$dbTranslation) {
            $dbTranslation['theme'] = self::THEME_NAME;
        }
        $databaseTranslationLoader = new MockDatabaseTranslationLoader($dbTranslations);
        $provider = new ThemeProvider(
            $databaseTranslationLoader,
            self::$tempDir
        );
        $provider->defaultTranslationDir = self::$tempDir;
        $provider->themeResourcesDirectory = self::$tempThemesDir;
        $provider->filesystem = new Filesystem();
        $catalogue = $provider->getDatabaseCatalogue();

        $domains = $catalogue->getDomains();
        $this->assertEmpty($domains);

        // Translations in DB have theme name, no theme selected in provider but given as parameter
        $catalogue = $provider->getDatabaseCatalogue(self::THEME_NAME);
        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'AdminSomeDomain',
            'ModulesDomainShop',
            'ShopDomain',
        ], $domains);

        $messages = $catalogue->all();
        foreach (self::$dbWordings as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }

        // Translations in DB have theme name, theme selected in provider and no parameter to method call
        $provider->setThemeName(self::THEME_NAME);
        $catalogue = $provider->getDatabaseCatalogue();
        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'AdminSomeDomain',
            'ModulesDomainShop',
            'ShopDomain',
        ], $domains);

        $messages = $catalogue->all();
        foreach (self::$dbWordings as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }

    public function testGetMessageCatalogue()
    {
        $provider = new ThemeProvider(
            $this->databaseTranslationLoader,
            self::$tempDir
        );
        $provider->defaultTranslationDir = self::$tempDir;
        $provider->themeResourcesDirectory = self::$tempThemesDir;
        $provider->filesystem = new Filesystem();
        $provider->setThemeName(self::THEME_NAME);

        $catalogue = $provider->getMessageCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'AdminSomeDomain',
            'ModulesDomainShop',
            'ShopDomain',
        ], $domains);

        $messages = $catalogue->all();
        $expectedWordings = [
            'ShopDomain' => [
                'Some wording' => 'File wording',
                'Some other wording' => 'File other wording',
            ],
            'ModulesDomainShop' => [
                'Foo' => 'File Foo',
                'Bar' => 'File Bar',
            ],
        ];
        foreach ($expectedWordings as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }

        // Test with theme setted
        $dbTranslations = self::$dbTranslations;
        foreach ($dbTranslations as &$dbTranslation) {
            $dbTranslation['theme'] = self::THEME_NAME;
        }
        $databaseTranslationLoader = new MockDatabaseTranslationLoader($dbTranslations);
        $provider = new ThemeProvider(
            $databaseTranslationLoader,
            self::$tempDir
        );
        $provider->defaultTranslationDir = self::$tempDir;
        $provider->themeResourcesDirectory = self::$tempThemesDir;
        $provider->filesystem = new Filesystem();
        $provider->setThemeName(self::THEME_NAME);

        $catalogue = $provider->getMessageCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'AdminSomeDomain',
            'ModulesDomainShop',
            'ShopDomain',
        ], $domains);

        $messages = $catalogue->all();
        $expectedWordings = [
            'ShopDomain' => [
                'Some wording' => 'DB wording',
                'Some other wording' => 'File other wording',
            ],
            'ModulesDomainShop' => [
                'Foo' => 'DB Foo',
                'Bar' => 'DB Bar',
            ],
        ];
        foreach ($expectedWordings as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }
}
