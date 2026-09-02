<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\PrestaShopBundle\Translation\Provider;

use PHPUnit\Framework\TestCase;
use PrestaShopBundle\Translation\Provider\ThemeProvider;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Test the provider of theme translations
 */
class ThemeProviderTest extends TestCase
{
    /**
     * @var ThemeProvider
     */
    private $provider;

    protected function setUp(): void
    {
        $loader = $this->getMockBuilder(LoaderInterface::class)
            ->getMock();

        $resourcesDir = __DIR__ . '/../../../../Resources/themes/fakeThemeForTranslations';

        $this->provider = new ThemeProvider($loader, $resourcesDir);
        $this->provider->filesystem = new Filesystem();
    }

    public function testItExtractsCatalogueFromXliffFiles()
    {
        $catalogue = $this->provider->getMessageCatalogue();
        $this->assertInstanceOf(MessageCatalogue::class, $catalogue);

        // Check integrity of translations
        $messages = $catalogue->all();
        $this->assertArrayHasKey('ShopTheme', $messages);
        $this->assertArrayHasKey('ShopThemeCustomeraccount', $messages);

        $this->assertCount(29, $catalogue->all('ShopTheme'));
        $this->assertSame('Contact us!', $catalogue->get('Contact us', 'ShopTheme'));
    }
}
