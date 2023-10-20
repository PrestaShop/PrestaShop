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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\PrestaShopBundle\Translation\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Provider\BackOfficeProvider;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Tests the provider for backoffice translations
 */
class BackOfficeProviderTest extends TestCase
{
    /**
     * @var BackOfficeProvider
     */
    private $provider;

    protected function setUp(): void
    {
        $loader = $this->getMockBuilder(LoaderInterface::class)
            ->getMock();

        $resourcesDir = __DIR__ . '/../../../../Resources/translations';

        $this->provider = new BackOfficeProvider($loader, $resourcesDir);
        $this->provider->setLocale('fr-FR');
    }

    /**
     * The provider should retrieve all translations from files that
     * look like `AdminSomething` or `ModulesSomethingAdmin`
     */
    public function testItExtractsCatalogueFromXliffFiles()
    {
        // The xliff file contains 38 keys
        $catalogue = $this->provider->getMessageCatalogue();
        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        $messages = $catalogue->all();

        // Check integrity of translations
        $this->assertArrayHasKey('AdminActions', $messages);
        $this->assertArrayHasKey('ModulesWirepaymentAdmin', $messages);

        $this->assertCount(91, $catalogue->all('AdminActions'));
        $this->assertSame('Télécharger le fichier', $catalogue->get('Download file', 'AdminActions'));

        $this->assertCount(21, $catalogue->all('ModulesWirepaymentAdmin'));
        $this->assertSame(
            'Aucune devise disponible pour ce module',
            $catalogue->get('No currency has been set for this module.', 'ModulesWirepaymentAdmin')
        );
    }
}
