<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\PrestaShopBundle\Translation\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Provider\ModulesProvider;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider for all the modules' translations
 */
class ModulesProviderTest extends TestCase
{
    /**
     * @var ModulesProvider
     */
    private $provider;

    protected function setUp(): void
    {
        $loader = $this->getMockBuilder(LoaderInterface::class)
            ->getMock();

        $resourcesDir = __DIR__ . '/../../../../Resources/translations';

        $this->provider = new ModulesProvider($loader, $resourcesDir);
        $this->provider->setLocale('fr-FR');
    }

    public function testItExtractsCatalogueFromXliffFiles()
    {
        $catalogue = $this->provider->getMessageCatalogue();
        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $this->assertArrayHasKey('ModulesWirepaymentAdmin', $messages);
        $this->assertArrayHasKey('ModulesWirepaymentShop', $messages);

        $this->assertCount(15, $catalogue->all('ModulesCheckpaymentAdmin'));
        $this->assertSame('Chèque', $catalogue->get('Payments by check', 'ModulesCheckpaymentAdmin'));

        $this->assertCount(19, $catalogue->all('ModulesCheckpaymentShop'));
        $this->assertSame('Payer par chèque', $catalogue->get('Pay by check', 'ModulesCheckpaymentShop'));
    }
}
