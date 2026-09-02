<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\PrestaShopBundle\Translation\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Provider\FrontOfficeProvider;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider of front office translations
 */
class FrontOfficeProviderTest extends TestCase
{
    /**
     * @var FrontOfficeProvider
     */
    private $provider;

    protected function setUp(): void
    {
        $loader = $this->getMockBuilder(LoaderInterface::class)
            ->getMock();

        $resourcesDir = __DIR__ . '/../../../../Resources/translations';

        $this->provider = new FrontOfficeProvider($loader, $resourcesDir);
        $this->provider->setLocale('fr-FR');
    }

    /**
     * The provider should retrieve all translations from files that
     * look like `ShopSomething` or `ModulesSomethingShop`
     */
    public function testItExtractsCatalogueFromXliffFiles()
    {
        // The xliff file contains 6 keys
        $catalogue = $this->provider->getMessageCatalogue();
        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $this->assertArrayHasKey('ShopNotificationsWarning', $messages);
        $this->assertArrayHasKey('ModulesWirepaymentShop', $messages);

        $this->assertCount(8, $catalogue->all('ShopNotificationsWarning'));
        $this->assertSame(
            'Vous ne possédez pas de bon de réduction.',
            $catalogue->get('You do not have any vouchers.', 'ShopNotificationsWarning')
        );

        $this->assertCount(20, $catalogue->all('ModulesWirepaymentShop'));
        $this->assertSame(
            'à l\'ordre de',
            $catalogue->get('Name of account owner', 'ModulesWirepaymentShop')
        );
    }
}
