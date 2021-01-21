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
use PrestaShopBundle\Translation\Provider\ExternalModuleLegacySystemProvider;
use PrestaShopBundle\Translation\Provider\SearchProvider;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\MessageCatalogue;

class SearchProviderTest extends TestCase
{
    /**
     * @var string
     */
    private static $tempDir;

    private static $defaultTranslations = [
        'AdminDomain' => [
            'Some wording' => 'Some wording',
            'Some other wording' => 'Some other wording',
            'Some third wording' => 'Some third wording',
        ],
        'ModulesDomainAdmin' => [
            'Foo' => 'Foo',
            'Bar' => 'Bar',
        ],
        'ModulesDomainSomeDomain' => [
            'Something' => 'Special',
            'OtherCommon' => 'Thing',
        ],
    ];

    private static $fileTranslations = [
        'AdminDomain' => [
            'Some wording' => 'File wording',
            'Some other wording' => 'File other wording',
        ],
        'ModulesDomainAdmin' => [
            'Foo' => 'File Foo',
            'Bar' => 'File Bar',
        ],
        'ModulesDomainSomeDomain' => [
            'Something' => 'File Special',
            'OtherCommon' => 'File Thing',
        ],
    ];

    private static $dbTranslations = [
        [
            'lang' => 'en-US',
            'key' => 'Some wording',
            'translation' => 'DB wording',
            'domain' => 'AdminDomain',
            'theme' => null,
        ],
        [
            'lang' => 'en-US',
            'key' => 'Foo',
            'translation' => 'DB Foo',
            'domain' => 'ModulesDomainAdmin',
            'theme' => null,
        ],
        [
            'lang' => 'en-US',
            'key' => 'Bar',
            'translation' => 'DB Bar',
            'domain' => 'ModulesDomainAdmin',
            'theme' => null,
        ],
        [
            'lang' => 'en-US',
            'key' => 'Something',
            'translation' => 'DB Special',
            'domain' => 'ModulesDomainSomeDomain',
            'theme' => null,
        ],
        [
            'lang' => 'en-US',
            'key' => 'OtherCommon',
            'translation' => 'DB Thing',
            'domain' => 'ModulesDomainSomeDomain',
            'theme' => null,
        ],
    ];

    private static $wordingsAdmin = [
        'AdminDomain' => [
            'Some wording' => 'Some wording',
            'Some other wording' => 'Some other wording',
            'Some third wording' => 'Some third wording',
        ],
    ];
    private static $wordingsModulesDomain = [
        'ModulesDomainAdmin' => [
            'Foo' => 'Foo',
            'Bar' => 'Bar',
        ],
        'ModulesDomainSomeDomain' => [
            'Something' => 'Special',
            'OtherCommon' => 'Thing',
        ],
    ];
    private static $wordingsEmptyModulesDomain = [
        'ModulesDomainAdmin.en-US' => [
            'Foo' => '',
            'Bar' => '',
        ],
        'ModulesDomainSomeDomain.en-US' => [
            'Something' => '',
            'OtherCommon' => '',
        ],
    ];

    private static $fileWordingsAdmin = [
        'AdminDomain' => [
            'Some wording' => 'File wording',
            'Some other wording' => 'File other wording',
        ],
    ];

    private static $fileWordingsModulesAdmin = [
        'ModulesDomainAdmin' => [
            'Foo' => 'File Foo',
            'Bar' => 'File Bar',
        ],
        'ModulesDomainSomeDomain' => [
            'Something' => 'File Special',
            'OtherCommon' => 'File Thing',
        ],
    ];

    private static $dbWordingsAdmin = [
        'AdminDomain' => [
            'Some wording' => 'DB wording',
        ],
    ];

    private static $dbWordingsModulesAdmin = [
        'ModulesDomainAdmin' => [
            'Foo' => 'DB Foo',
            'Bar' => 'DB Bar',
        ],
        'ModulesDomainSomeDomain' => [
            'Something' => 'DB Special',
            'OtherCommon' => 'DB Thing',
        ],
    ];

    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseTranslationLoader;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|ExternalModuleLegacySystemProvider
     */
    private $externalModuleLegacySystemProvider;

    public function setUp()
    {
        self::$tempDir = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), 'SearchProviderTest']);

        $this->externalModuleLegacySystemProvider = $this->getMockBuilder(ExternalModuleLegacySystemProvider::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        // Default catalogue
        $catalogue = new MessageCatalogue(SearchProvider::DEFAULT_LOCALE);
        foreach (self::$defaultTranslations as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        (new XliffFileDumper())->dump($catalogue, [
            'path' => self::$tempDir . DIRECTORY_SEPARATOR . 'default',
        ]);

        // Xliff catalogue
        $catalogue = new MessageCatalogue(SearchProvider::DEFAULT_LOCALE);
        foreach (self::$fileTranslations as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        (new XliffFileDumper())->dump($catalogue, [
            'path' => self::$tempDir . DIRECTORY_SEPARATOR . SearchProvider::DEFAULT_LOCALE,
        ]);

        // Database catalogue
        $this->databaseTranslationLoader = new MockDatabaseTranslationLoader(self::$dbTranslations);
    }

    public function testGetDefaultCatalogue()
    {
        $provider = new SearchProvider(
            $this->databaseTranslationLoader,
            $this->externalModuleLegacySystemProvider,
            self::$tempDir,
            self::$tempDir
        );
        $catalogue = $provider
            ->setDomain('Admin')
            ->getDefaultCatalogue(false);

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'AdminDomain',
        ], $domains);

        $messages = $catalogue->all();
        foreach (self::$wordingsAdmin as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }

        $catalogue = $provider
            ->setDomain('ModulesDomain')
            ->getDefaultCatalogue(false);

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'ModulesDomainAdmin',
            'ModulesDomainSomeDomain',
        ], $domains);

        $messages = $catalogue->all();
        foreach (self::$wordingsModulesDomain as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }

    public function testGetEmptyDefaultCatalogue()
    {
        $provider = new SearchProvider(
            $this->databaseTranslationLoader,
            $this->externalModuleLegacySystemProvider,
            self::$tempDir,
            self::$tempDir
        );

        $catalogue = $provider
            ->setDomain('ModulesDomain')
            ->getDefaultCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'ModulesDomainAdmin',
            'ModulesDomainSomeDomain',
        ], $domains);

        $messages = $catalogue->all();
        foreach (self::$wordingsModulesDomain as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }

        // catalogue will be emptied if locale != DEFAULT_LOCALE
        $provider->setLocale('ab-AB');
        $catalogue = $provider
            ->setDomain('ModulesDomain')
            ->getDefaultCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'ModulesDomainAdmin.en-US',
            'ModulesDomainSomeDomain.en-US',
        ], $domains);

        $messages = $catalogue->all();
        foreach (self::$wordingsEmptyModulesDomain as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }

    public function testGetXliffCatalogue()
    {
        $provider = new SearchProvider(
            $this->databaseTranslationLoader,
            $this->externalModuleLegacySystemProvider,
            self::$tempDir,
            self::$tempDir
        );
        $catalogue = $provider
            ->setDomain('Admin')
            ->getXliffCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'AdminDomain',
        ], $domains);

        $messages = $catalogue->all();
        foreach (self::$fileWordingsAdmin as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }

        $catalogue = $provider
            ->setDomain('ModulesDomain')
            ->getXliffCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'ModulesDomainAdmin',
            'ModulesDomainSomeDomain',
        ], $domains);

        $messages = $catalogue->all();
        foreach (self::$fileWordingsModulesAdmin as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }

    public function testGetDatabaseCatalogue()
    {
        $provider = new SearchProvider(
            $this->databaseTranslationLoader,
            $this->externalModuleLegacySystemProvider,
            self::$tempDir,
            self::$tempDir
        );
        $catalogue = $provider
            ->setDomain('Admin')
            ->getDatabaseCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'AdminDomain',
        ], $domains);

        $messages = $catalogue->all();
        foreach (self::$dbWordingsAdmin as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }

        $catalogue = $provider
            ->setDomain('ModulesDomain')
            ->getDatabaseCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'ModulesDomainAdmin',
            'ModulesDomainSomeDomain',
        ], $domains);

        $messages = $catalogue->all();
        foreach (self::$dbWordingsModulesAdmin as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }

    public function testGetMessageCatalogue()
    {
        $provider = new SearchProvider(
            $this->databaseTranslationLoader,
            $this->externalModuleLegacySystemProvider,
            self::$tempDir,
            self::$tempDir
        );
        $catalogue = $provider
            ->setDomain('Admin')
            ->getMessageCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'AdminDomain',
        ], $domains);

        $messages = $catalogue->all();
        $expectedWordings = [
            'AdminDomain' => [
                'Some wording' => 'DB wording',
                'Some other wording' => 'File other wording',
                'Some third wording' => 'Some third wording',
            ],
        ];
        foreach ($expectedWordings as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }

        $catalogue = $provider
            ->setDomain('ModulesDomain')
            ->getMessageCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'ModulesDomainAdmin',
            'ModulesDomainSomeDomain',
        ], $domains);

        $messages = $catalogue->all();
        $expectedWordings = [
            'ModulesDomainAdmin' => [
                'Foo' => 'DB Foo',
                'Bar' => 'DB Bar',
            ],
        ];
        foreach ($expectedWordings as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }
}
