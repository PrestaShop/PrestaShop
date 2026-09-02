<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\PrestaShopBundle\Translation\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Provider\ModuleProvider;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider of specific module translations
 */
class ModuleProviderTest extends TestCase
{
    /**
     * @var ModuleProvider
     */
    private $provider;

    protected function setUp(): void
    {
        $loader = $this->getMockBuilder(LoaderInterface::class)
            ->getMock();

        $resourcesDir = __DIR__ . '/../../../../Resources/translations';

        $this->provider = new ModuleProvider($loader, $resourcesDir);
        $this->provider->setLocale('fr-FR');
        $this->provider->setModuleName('ps_wirepayment');
    }

    public function testItExtractsCatalogueFromXliffFiles()
    {
        $catalogue = $this->provider->getMessageCatalogue();

        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // check only the specific module translations have been loaded
        $domains = $catalogue->getDomains();
        // for some reason domains may not be in the same order as the test
        sort($domains);
        $this->assertSame(['ModulesWirepaymentAdmin', 'ModulesWirepaymentShop'], $domains);

        // Check integrity of translations
        $this->assertCount(21, $catalogue->all('ModulesWirepaymentAdmin'));

        $this->assertSame('Transfert bancaire', $catalogue->get('Wire payment', 'ModulesWirepaymentAdmin'));

        $this->assertCount(20, $catalogue->all('ModulesWirepaymentShop'));
        $this->assertSame('Payer par virement bancaire', $catalogue->get('Pay by bank wire', 'ModulesWirepaymentShop'));
    }
}
