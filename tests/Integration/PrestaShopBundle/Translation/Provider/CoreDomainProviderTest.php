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

use Language;
use PrestaShopBundle\Translation\Provider\CoreProvider;
use PrestaShopBundle\Translation\Provider\Type\CoreDomainType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Tests the search translations provider
 */
class CoreDomainProviderTest extends KernelTestCase
{
    /**
     * @var CoreProvider
     */
    private $provider;

    protected function setUp()
    {
        self::bootKernel();
        $resourcesDir = __DIR__ . '/../../../../Resources/translations';
        $databaseContent = [
            [
                'lang' => 'fr-FR',
                'key' => 'Some text to translate',
                'translation' => 'Traduction customisée',
                'domain' => 'AdminActions',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Some other text to translate',
                'translation' => 'Traduction de texte aléatoire',
                'domain' => 'AdminActionsNumberTwo',
                'theme' => null,
            ],
            [
                'lang' => 'fr-FR',
                'key' => 'Some made up text',
                'translation' => 'Un texte inventé',
                'domain' => 'AdminActions',
                'theme' => 'classic',
            ],
        ];
        $type = new CoreDomainType('AdminActions');
        $this->provider = new CoreProvider(
            new MockDatabaseTranslationReader($databaseContent),
            $resourcesDir,
            $type->getFilenameFilters(),
            $type->getTranslationDomains()
        );

        $langId = Language::getIdByIso('fr', true);
        if (!$langId) {
            $lang = new Language();
            $lang->locale = 'fr-FR';
            $lang->iso_code = 'fr';
            $lang->name = 'Français';
            $lang->add();
        }
    }

    public function testItExtractsOnlyTheSelectedCataloguesFromXliffFiles()
    {
        $catalogue = $this->provider->getDefaultCatalogue('fr-FR', false);
        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check that only the selected domain is in the catalogue
        $this->assertSame(['AdminActions'], $catalogue->getDomains());

        // Check integrity of translations
        $this->assertArrayHasKey('AdminActions', $catalogue->all());

        $adminTranslations = $catalogue->all('AdminActions');
        $this->assertCount(91, $adminTranslations);
        $this->assertSame('Download file', $catalogue->get('Download file', 'AdminActions'));

        $catalogue = $this->provider->getFileTranslatedCatalogue('fr-FR');
        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check that only the selected domain is in the catalogue
        $this->assertSame(['AdminActions'], $catalogue->getDomains());

        // Check integrity of translations
        $this->assertArrayHasKey('AdminActions', $catalogue->all());

        $adminTranslations = $catalogue->all('AdminActions');
        // There is no translation for 'Continue'
        $this->assertCount(90, $adminTranslations);
        $this->assertSame('Télécharger le fichier', $catalogue->get('Download file', 'AdminActions'));
    }

    public function testItLoadsCustomizedTranslationsFromDatabase()
    {
        // load catalogue from database translations
        $catalogue = $this->provider->getUserTranslatedCatalogue('fr-FR');

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $domains = $catalogue->getDomains();
        sort($domains);

        // verify all catalogues are loaded
        $this->assertSame([
            'AdminActions',
            'AdminActionsNumberTwo',
        ], $domains);

        // verify that the catalogues are complete
        $this->assertCount(1, $messages['AdminActions']);

        // verify translations
        $this->assertSame('Traduction customisée', $catalogue->get('Some text to translate', 'AdminActions'));
    }

    protected function tearDown()
    {
        $langId = Language::getIdByIso('fr', true);
        if ($langId) {
            \Db::getInstance()->execute(
                'DELETE FROM `' . _DB_PREFIX_ . 'lang` WHERE id_lang = ' . $langId
            );
        }
        self::$kernel->shutdown();
    }
}
