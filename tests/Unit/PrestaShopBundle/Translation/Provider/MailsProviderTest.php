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
use PrestaShopBundle\Translation\Provider\MailsProvider;
use Symfony\Component\Translation\Dumper\XliffFileDumper;
use Symfony\Component\Translation\MessageCatalogue;

class MailsProviderTest extends TestCase
{
    /**
     * @var string
     */
    private static $tempDir;

    private static $defaultTranslations = [
        'EmailsSubject' => [
            'Some wording' => 'Some wording',
            'Some other wording' => 'Some other wording',
            'Some third wording' => 'Some third wording',
        ],
        'ModulesDomainAdmin' => [
            'Foo' => 'Foo',
            'Bar' => 'Bar',
        ],
        'ShopSomeDomain' => [
            'Something' => 'Special',
            'OtherCommon' => 'Thing',
        ],
    ];

    private static $fileTranslations = [
        'EmailsSubject' => [
            'Some wording' => 'File wording',
            'Some other wording' => 'File other wording',
        ],
        'ModulesDomainAdmin' => [
            'Foo' => 'File Foo',
            'Bar' => 'File Bar',
        ],
        'ShopSomeDomain' => [
            'Something' => 'File Special',
            'OtherCommon' => 'File Thing',
        ],
    ];

    private static $dbTranslations = [
        [
            'lang' => 'en-US',
            'key' => 'Some wording',
            'translation' => 'DB wording',
            'domain' => 'EmailsSubject',
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
            'domain' => 'ShopSomeDomain',
            'theme' => null,
        ],
        [
            'lang' => 'en-US',
            'key' => 'OtherCommon',
            'translation' => 'DB Thing',
            'domain' => 'ShopSomeDomain',
            'theme' => null,
        ],
    ];

    private static $wordings = [
        'EmailsSubject' => [
            'Some wording' => 'Some wording',
            'Some other wording' => 'Some other wording',
            'Some third wording' => 'Some third wording',
        ],
    ];

    private static $emptyWordings = [
        'EmailsSubject.en-US' => [
            'Some wording' => '',
            'Some other wording' => '',
            'Some third wording' => '',
        ],
    ];

    private static $fileWordings = [
        'EmailsSubject' => [
            'Some wording' => 'File wording',
            'Some other wording' => 'File other wording',
        ],
    ];

    private static $dbWordings = [
        'EmailsSubject' => [
            'Some wording' => 'DB wording',
        ],
    ];

    /**
     * @var DatabaseTranslationLoader
     */
    private $databaseTranslationLoader;

    public function setUp()
    {
        self::$tempDir = implode(DIRECTORY_SEPARATOR, [sys_get_temp_dir(), 'MailsProviderTest']);

        // Default catalogue
        $catalogue = new MessageCatalogue(MailsProvider::DEFAULT_LOCALE);
        foreach (self::$defaultTranslations as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        (new XliffFileDumper())->dump($catalogue, [
            'path' => self::$tempDir . DIRECTORY_SEPARATOR . 'default',
        ]);

        // Xliff catalogue
        $catalogue = new MessageCatalogue(MailsProvider::DEFAULT_LOCALE);
        foreach (self::$fileTranslations as $domain => $messages) {
            $catalogue->add($messages, $domain);
        }
        (new XliffFileDumper())->dump($catalogue, [
            'path' => self::$tempDir . DIRECTORY_SEPARATOR . MailsProvider::DEFAULT_LOCALE,
        ]);

        // Database catalogue
        $this->databaseTranslationLoader = new MockDatabaseTranslationLoader(self::$dbTranslations);
    }

    public function testGetDefaultCatalogue()
    {
        $catalogue = (new MailsProvider(
            $this->databaseTranslationLoader,
            self::$tempDir
        ))
            ->getDefaultCatalogue(false);

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'EmailsSubject',
        ], $domains);

        $messages = $catalogue->all();
        foreach (self::$wordings as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }

    public function testGetEmptyDefaultCatalogue()
    {
        $provider = (new MailsProvider(
            $this->databaseTranslationLoader,
            self::$tempDir
        ));
        $catalogue = $provider->getDefaultCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'EmailsSubject',
        ], $domains);

        // catalogue won't be emptied if locale = DEFAULT_LOCALE
        $messages = $catalogue->all();
        foreach (self::$wordings as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }

        $provider->setLocale('ab-AB');
        $catalogue = $provider->getDefaultCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'EmailsSubject.en-US',
        ], $domains);

        // catalogue will be emptied if locale != DEFAULT_LOCALE
        $messages = $catalogue->all();
        foreach (self::$emptyWordings as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }

    public function testGetXliffCatalogue()
    {
        $catalogue = (new MailsProvider(
            $this->databaseTranslationLoader,
            self::$tempDir
        ))
            ->getXliffCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'EmailsSubject',
        ], $domains);

        $messages = $catalogue->all();
        foreach (self::$fileWordings as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }

    public function testGetDatabaseCatalogue()
    {
        $catalogue = (new MailsProvider(
            $this->databaseTranslationLoader,
            self::$tempDir
        ))
            ->getDatabaseCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'EmailsSubject',
        ], $domains);

        $messages = $catalogue->all();
        foreach (self::$dbWordings as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }

    public function testGetMessageCatalogue()
    {
        $catalogue = (new MailsProvider(
            $this->databaseTranslationLoader,
            self::$tempDir
        ))
            ->getMessageCatalogue();

        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame([
            'EmailsSubject',
        ], $domains);

        $messages = $catalogue->all();
        $expectedWordings = [
            'EmailsSubject' => [
                'Some wording' => 'DB wording',
                'Some other wording' => 'File other wording',
                'Some third wording' => 'Some third wording',
            ],
        ];
        foreach ($expectedWordings as $key => $value) {
            $this->assertSame($value, $messages[$key]);
        }
    }
}
