<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
