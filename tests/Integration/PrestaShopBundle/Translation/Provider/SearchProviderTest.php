<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\PrestaShopBundle\Translation\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Provider\ExternalModuleLegacySystemProvider;
use PrestaShopBundle\Translation\Provider\SearchProvider;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Tests the search translations provider
 */
class SearchProviderTest extends TestCase
{
    /**
     * @var SearchProvider
     */
    private $provider;

    protected function setUp(): void
    {
        $loader = $this->getMockBuilder(LoaderInterface::class)
            ->getMock()
        ;
        $externalSystemProvider = $this->getMockBuilder(ExternalModuleLegacySystemProvider::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $resourcesDir = __DIR__ . '/../../../../Resources/translations';
        $this->provider = new SearchProvider(
            $loader,
            $externalSystemProvider,
            $resourcesDir
        );

        $this->provider->setDomain('AdminActions');
        $this->provider->setLocale('fr-FR');
    }

    public function testItExtractsOnlyTheSelectedCataloguesFromXliffFiles()
    {
        $catalogue = $this->provider->getMessageCatalogue();
        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check that only the selected domain is in the catalogue
        $this->assertSame(['AdminActions'], $catalogue->getDomains());

        // Check integrity of translations
        $this->assertArrayHasKey('AdminActions', $catalogue->all());

        $adminTranslations = $catalogue->all('AdminActions');
        $this->assertCount(91, $adminTranslations);
        $this->assertSame('Télécharger le fichier', $catalogue->get('Download file', 'AdminActions'));
    }
}
