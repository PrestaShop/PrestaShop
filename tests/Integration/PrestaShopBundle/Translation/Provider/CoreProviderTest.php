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

namespace Tests\Integration\PrestaShopBundle\Translation\Provider;

use PrestaShopBundle\Translation\Loader\DatabaseTranslationReader;
use PrestaShopBundle\Translation\Provider\CoreProvider;
use PrestaShopBundle\Translation\Provider\Type\BackOfficeType;
use PrestaShopBundle\Translation\Provider\Type\CoreFrontType;
use PrestaShopBundle\Translation\Provider\Type\MailsBodyType;
use PrestaShopBundle\Translation\Provider\Type\MailsType;
use PrestaShopBundle\Translation\Provider\Type\OthersType;
use PrestaShopBundle\Translation\Provider\Type\TypeInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider of back translations
 */
class CoreProviderTest extends KernelTestCase
{
    /**
     * @var string
     */
    private $translationsDir;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|DatabaseTranslationReader
     */
    private $databaseReader;

    public function setUp()
    {
        self::bootKernel();
        $this->translationsDir = self::$kernel->getContainer()->getParameter('translations_dir');

        $databaseContent = [
            [
                'lang' => 'fr-FR',
                'key' => 'Uninstall',
                'translation' => 'Traduction customisée',
                'domain' => 'AdminActions',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Some made up text',
                'translation' => 'Un texte inventé',
                'domain' => 'ShopActions',
                'theme' => 'classic',
            ],
        ];

        $this->databaseReader = new MockDatabaseTranslationReader($databaseContent);
    }

    /**
     * Test it loads a XLIFF catalogue from the locale's `translations` directory
     *
     * @dataProvider provideLoadsCatalogueFromXliffFilesInLocaleDirectory
     */
    public function testItLoadsCatalogueFromXliffFilesInLocaleDirectory(
        array $databaseContent,
        TypeInterface $providerType,
        array $expectedDomains,
        string $expectedFirstDomain,
        int $expectedFirstDomainCount,
        array $expectedFirstDomainTranslations
    ) {
        $provider = new CoreProvider(
            new MockDatabaseTranslationReader($databaseContent),
            $this->translationsDir,
            $providerType->getFilenameFilters(),
            $providerType->getTranslationDomains()
        );

        // load catalogue from translations/fr-FR
        $catalogue = $provider->getFileTranslatedCatalogue('fr-FR');

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        $this->assertSame($expectedFirstDomain, array_keys($messages)[0]);

        // verify all catalogues are loaded
        $this->assertSame($expectedDomains, $domains);

        // verify that the catalogues are complete
        $this->assertCount($expectedFirstDomainCount, $messages[$expectedFirstDomain]);

        foreach ($expectedFirstDomainTranslations as $translationKey => $translationValue) {
            $this->assertSame($translationValue, $catalogue->get($translationKey, $expectedFirstDomain));
        }
    }

    public function provideLoadsCatalogueFromXliffFilesInLocaleDirectory()
    {
        return [
            'back' => [
                $this->getBackDatabaseContent(),
                new BackOfficeType(),
                ['AdminActions'],
                'AdminActions',
                90,
                [
                    'Save and stay' => 'Enregistrer et rester',
                    'Uninstall' => 'Désinstaller',
                ],
            ],
            'core_front' => [
                $this->getCoreFrontDatabaseContent(),
                new CoreFrontType(),
                ['ShopNotificationsWarning'],
                'ShopNotificationsWarning',
                8,
                [
                    'You cannot return merchandise with a guest account.' => 'Vous ne pouvez pas solliciter un retour de marchandise avec un compte invité.',
                    'You do not have any vouchers.' => 'Vous ne possédez pas de bon de réduction.',
                ],
            ],
            'mails_body' => [
                $this->getMailsBodyDatabaseContent(),
                new MailsBodyType(),
                ['EmailsBody'],
                'EmailsBody',
                21,
                [
                    'We will answer as soon as possible.' => 'Nous vous répondrons dans les meilleurs délais.',
                    'Some made up text' => 'Some made up text',
                ],
            ],
            'mails' => [
                $this->getMailsDatabaseContent(),
                new MailsType(),
                ['EmailsSubject'],
                'EmailsSubject',
                24,
                [
                    'Your order return status has changed' => 'L\'état de votre retour produit a été modifié',
                    'Some made up text' => 'Some made up text',
                ],
            ],
            'others' => [
                $this->getOthersDatabaseContent(),
                new OthersType(),
                ['messages'],
                'messages',
                164,
                [
                    '%d product(s) successfully created.' => '%d produit(s) créé(s) avec succès.',
                    'Some made up text' => 'Some made up text',
                ],
            ],
        ];
    }

    /**
     * Test it loads a default catalogue from the `translations` default directory
     *
     * @dataProvider provideExtractsDefaultCatalogueFromTranslationsDefaultFiles
     */
    public function testItExtractsDefaultCatalogueFromTranslationsDefaultFiles(
        array $databaseContent,
        TypeInterface $providerType,
        array $expectedDomains,
        string $expectedFirstDomain,
        int $expectedFirstDomainCount,
        array $expectedFirstDomainTranslations
    ) {
        $provider = new CoreProvider(
            new MockDatabaseTranslationReader($databaseContent),
            $this->translationsDir,
            $providerType->getFilenameFilters(),
            $providerType->getTranslationDomains()
        );

        // load catalogue from translations/default
        $catalogue = $provider->getDefaultCatalogue('fr-FR', false);

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        // verify all catalogues are loaded
        $this->assertSame($expectedDomains, $domains);

        // verify that the catalogues are complete
        $this->assertCount($expectedFirstDomainCount, $messages[$expectedFirstDomain]);

        foreach ($expectedFirstDomainTranslations as $translationKey => $translationValue) {
            $this->assertSame($translationValue, $catalogue->get($translationKey, $expectedFirstDomain));
        }

        // test get empty catalogue
        $catalogue = $provider->getDefaultCatalogue('fr-FR');

        foreach ($expectedFirstDomainTranslations as $translationKey => $translationValue) {
            $this->assertSame('', $catalogue->get($translationKey, $expectedFirstDomain));
        }
    }

    public function provideExtractsDefaultCatalogueFromTranslationsDefaultFiles()
    {
        return [
            'back' => [
                $this->getBackDatabaseContent(),
                new BackOfficeType(),
                ['AdminActions'],
                'AdminActions',
                91,
                [
                    'Save and stay' => 'Save and stay',
                    'Uninstall' => 'Uninstall',
                ],
            ],
            'core_front' => [
                $this->getCoreFrontDatabaseContent(),
                new CoreFrontType(),
                ['ShopNotificationsWarning'],
                'ShopNotificationsWarning',
                8,
                [
                    'You cannot return merchandise with a guest account.' => 'You cannot return merchandise with a guest account.',
                    'You do not have any vouchers.' => 'You do not have any vouchers.',
                ],
            ],
            'mails_body' => [
                $this->getMailsBodyDatabaseContent(),
                new MailsBodyType(),
                ['EmailsBody'],
                'EmailsBody',
                23,
                [
                    'We will answer as soon as possible.' => 'We will answer as soon as possible.',
                    '(waiting for validation)' => '(waiting for validation)',
                ],
            ],
            'mails' => [
                $this->getMailsDatabaseContent(),
                new MailsType(),
                ['EmailsSubject'],
                'EmailsSubject',
                24,
                [
                    'Your order return status has changed' => 'Your order return status has changed',
                    'Order confirmation' => 'Order confirmation',
                ],
            ],
            'others' => [
                $this->getOthersDatabaseContent(),
                new OthersType(),
                ['messages'],
                'messages',
                522,
                [
                    '%d product(s) successfully created.' => '%d product(s) successfully created.',
                    'Attributes generator' => 'Attributes generator',
                ],
            ],
        ];
    }

    /**
     * @dataProvider provideLoadsCustomizedTranslationsFromDatabase
     */
    public function testItLoadsCustomizedTranslationsFromDatabase(
        array $databaseContent,
        TypeInterface $providerType,
        array $expectedDomains,
        string $expectedFirstDomain,
        int $expectedFirstDomainCount,
        array $expectedFirstDomainTranslations
    ) {
        $provider = new CoreProvider(
            new MockDatabaseTranslationReader($databaseContent),
            $this->translationsDir,
            $providerType->getFilenameFilters(),
            $providerType->getTranslationDomains()
        );

        // load catalogue from database translations
        $catalogue = $provider->getUserTranslatedCatalogue('fr-FR');

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        // verify all catalogues are loaded
        $this->assertSame($expectedDomains, $domains);

        // verify that the catalogues are complete
        $this->assertCount($expectedFirstDomainCount, $messages[$expectedFirstDomain]);

        foreach ($expectedFirstDomainTranslations as $translationKey => $translationValue) {
            $this->assertSame($translationValue, $catalogue->get($translationKey, $expectedFirstDomain));
        }
    }

    public function provideLoadsCustomizedTranslationsFromDatabase(): array
    {
        return [
            'back' => [
                $this->getBackDatabaseContent(),
                new BackOfficeType(),
                ['AdminActions'],
                'AdminActions',
                1,
                [
                    'Save and stay' => 'Save and stay',
                    'Uninstall' => 'Traduction customisée',
                ],
            ],
            'core_front' => [
                $this->getCoreFrontDatabaseContent(),
                new CoreFrontType(),
                ['ShopNotificationsWarning'],
                'ShopNotificationsWarning',
                1,
                [
                    'You cannot return merchandise with a guest account.' => 'You cannot return merchandise with a guest account.',
                    'You do not have any vouchers.' => 'Traduction customisée',
                ],
            ],
            'mails_body' => [
                $this->getMailsBodyDatabaseContent(),
                new MailsBodyType(),
                ['EmailsBody'],
                'EmailsBody',
                1,
                [
                    'We will answer as soon as possible.' => 'We will answer as soon as possible.',
                    '(waiting for validation)' => 'Traduction customisée',
                ],
            ],
            'mails' => [
                $this->getMailsDatabaseContent(),
                new MailsType(),
                ['EmailsSubject'],
                'EmailsSubject',
                1,
                [
                    'Your order return status has changed' => 'Your order return status has changed',
                    'Order confirmation' => 'Traduction customisée',
                ],
            ],
            'others' => [
                $this->getOthersDatabaseContent(),
                new OthersType(),
                ['messages'],
                'messages',
                1,
                [
                    '%d product(s) successfully created.' => '%d product(s) successfully created.',
                    'Attributes generator' => 'Traduction customisée',
                ],
            ],
        ];
    }

    private function getBackDatabaseContent(): array
    {
        return [
            [
                'lang' => 'fr-FR',
                'key' => 'Uninstall',
                'translation' => 'Traduction customisée',
                'domain' => 'AdminActions',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Some made up text',
                'translation' => 'Un texte inventé',
                'domain' => 'ShopActions',
                'theme' => 'classic',
            ],
        ];
    }

    private function getCoreFrontDatabaseContent(): array
    {
        return [
            [
                'lang' => 'fr-FR',
                'key' => 'You do not have any vouchers.',
                'translation' => 'Traduction customisée',
                'domain' => 'ShopNotificationsWarning',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'You have not received any credit slips.',
                'translation' => 'Un texte inventé',
                'domain' => 'ShopNotificationsWarning',
                'theme' => 'classic',
            ],
        ];
    }

    private function getMailsBodyDatabaseContent(): array
    {
        return [
            [
                'lang' => 'fr-FR',
                'key' => '(waiting for validation)',
                'translation' => 'Traduction customisée',
                'domain' => 'EmailsBody',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Some made up text',
                'translation' => 'Un texte inventé',
                'domain' => 'EmailsBody',
                'theme' => 'classic',
            ],
        ];
    }

    private function getMailsDatabaseContent(): array
    {
        return [
            [
                'lang' => 'fr-FR',
                'key' => 'Order confirmation',
                'translation' => 'Traduction customisée',
                'domain' => 'EmailsSubject',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Some made up text',
                'translation' => 'Un texte inventé',
                'domain' => 'EmailsSubject',
                'theme' => 'classic',
            ],
        ];
    }

    private function getOthersDatabaseContent(): array
    {
        return [
            [
                'lang' => 'fr-FR',
                'key' => 'Attributes generator',
                'translation' => 'Traduction customisée',
                'domain' => 'messages',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Some made up text',
                'translation' => 'Un texte inventé',
                'domain' => 'messages',
                'theme' => 'classic',
            ],
        ];
    }
}
