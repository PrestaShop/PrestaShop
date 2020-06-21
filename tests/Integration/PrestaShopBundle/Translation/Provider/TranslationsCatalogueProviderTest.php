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

use PrestaShopBundle\Translation\Provider\TranslationsCatalogueProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TranslationsCatalogueProviderTest extends KernelTestCase
{
    /**
     * @var TranslationsCatalogueProvider
     */
    private $provider;

    protected function setUp()
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $this->provider = $container->get('prestashop.translation.translations_provider');

        $langId = \Language::getIdByIso('fr');
        if (!$langId) {
            $lang = new \Language();
            $lang->locale = 'fr-FR';
            $lang->iso_code = 'fr';
            $lang->name = 'Français';
            $lang->add();
        }
    }

    /**
     * The provider should retrieve all translations from files that
     * look like `AdminSomething` or `ModulesSomethingAdmin`
     */
    public function testGetCatalogueForBackoffice()
    {
        $messages = $this->provider->getCatalogue('back', 'fr-FR');
        $this->assertIsArray($messages);

        // Check integrity of translations
        $this->assertArrayHasKey('AdminActions', $messages);
        $this->assertArrayHasKey('ModulesWirepaymentAdmin', $messages);

        $this->assertCount(92, $messages['AdminActions']);
        $this->assertArrayHasKey('__metadata', $messages['AdminActions']);
        $this->assertArrayHasKey('missing_translations', $messages['AdminActions']['__metadata']);
        $this->assertSame(91, $messages['AdminActions']['__metadata']['count']);
        $this->assertArrayHasKey('Download file', $messages['AdminActions']);
        $this->assertSame([
            'default' => 'Download file',
            'xliff' => 'Télécharger le fichier',
            'database' => null,
        ], $messages['AdminActions']['Download file']);

        $this->assertCount(22, $messages['ModulesWirepaymentAdmin']);
        $this->assertSame(
            [
                'default' => 'No currency has been set for this module.',
                'xliff' => 'Aucune devise disponible pour ce module',
                'database' => null,
            ],
            $messages['ModulesWirepaymentAdmin']['No currency has been set for this module.']
        );
    }

    /**
     * The provider should retrieve all translations from files that
     * look like `AdminSomething` or `ModulesSomethingAdmin`
     */
    public function testGetCatalogueForFrontoffice()
    {
        $messages = $this->provider->getCatalogue('front', 'fr-FR');
        $this->assertIsArray($messages);

        // Check integrity of translations
        $this->assertArrayHasKey('ShopNotificationsWarning', $messages);
        $this->assertArrayHasKey('ModulesWirepaymentShop', $messages);

        $this->assertCount(9, $messages['ShopNotificationsWarning']);
        $this->assertArrayHasKey('You do not have any vouchers.', $messages['ShopNotificationsWarning']);
        $this->assertSame(
            [
                'default' => 'You do not have any vouchers.',
                'xliff' => 'Vous ne possédez pas de bon de réduction.',
                'database' => null,
            ],
            $messages['ShopNotificationsWarning']['You do not have any vouchers.']
        );
        $this->assertArrayHasKey('__metadata', $messages['ShopNotificationsWarning']);
        $this->assertArrayHasKey('missing_translations', $messages['ShopNotificationsWarning']['__metadata']);
        $this->assertSame(8, $messages['ShopNotificationsWarning']['__metadata']['count']);

        $this->assertCount(21, $messages['ModulesWirepaymentShop']);
        $this->assertSame(20, $messages['ModulesWirepaymentShop']['__metadata']['count']);
        $this->assertArrayHasKey('Name of account owner', $messages['ModulesWirepaymentShop']);
        $this->assertSame(
            [
                'default' => 'Name of account owner',
                'xliff' => 'à l\'ordre de',
                'database' => null,
            ],
            $messages['ModulesWirepaymentShop']['Name of account owner']
        );
    }

    /**
     * The provider should retrieve all translations from files that
     * look like `AdminSomething` or `ModulesSomethingAdmin`
     */
    public function testGetCatalogueForModules()
    {
        $messages = $this->provider->getCatalogue('modules', 'fr-FR');
        $this->assertIsArray($messages);

        // check only the specific module translations have been loaded
        $domains = array_keys($messages);
        // for some reason domains may not be in the same order as the test
        sort($domains);
        $this->assertSame(['ModulesCheckpaymentAdmin', 'ModulesCheckpaymentShop', 'ModulesWirepaymentAdmin', 'ModulesWirepaymentShop'], $domains);

        // Check integrity of translations
        $this->assertCount(22, $messages['ModulesWirepaymentAdmin']);
        $this->assertArrayHasKey('__metadata', $messages['ModulesWirepaymentAdmin']);
        $this->assertArrayHasKey('missing_translations', $messages['ModulesWirepaymentAdmin']['__metadata']);
        $this->assertSame(21, $messages['ModulesWirepaymentAdmin']['__metadata']['count']);

        $this->assertArrayHasKey('Wire payment', $messages['ModulesWirepaymentAdmin']);
        $this->assertSame(
            [
                'default' => 'Wire payment',
                'xliff' => 'Transfert bancaire',
                'database' => null,
            ],
            $messages['ModulesWirepaymentAdmin']['Wire payment']
        );

        $this->assertCount(21, $messages['ModulesWirepaymentShop']);
        $this->assertSame(20, $messages['ModulesWirepaymentShop']['__metadata']['count']);
        $this->assertArrayHasKey('Wire payment', $messages['ModulesWirepaymentAdmin']);
        $this->assertSame(
            [
                'default' => 'Pay by bank wire',
                'xliff' => 'Payer par virement bancaire',
                'database' => null,
            ],
            $messages['ModulesWirepaymentShop']['Pay by bank wire']
        );
    }

    /**
     * The provider should retrieve all translations from files that
     * look like `AdminSomething` or `ModulesSomethingAdmin`
     */
    public function testGetDomainCatalogueForModule()
    {
        $messages = $this->provider->getDomainCatalogue('fr-FR', 'ModulesCheckpaymentShop');
        $this->assertIsArray($messages);

        // Check integrity of translations
        $this->assertCount(19, $messages);
        $this->assertArrayHasKey('default', $messages[0]);
        $this->assertArrayHasKey('xliff', $messages[0]);
        $this->assertArrayHasKey('database', $messages[0]);
        $this->assertArrayHasKey('tree_domain', $messages[0]);
        $this->assertSame([
            'Modules',
            'Checkpayment',
            'Shop',
        ], $messages[0]['tree_domain']);
    }

    protected function tearDown()
    {
        $langId = \Language::getIdByIso('fr');
        if ($langId) {
            (new \Language($langId))->delete();
        }
        self::$kernel->shutdown();
    }
}
