<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Shop;

function move_uploaded_file($from, $to)
{
    return true;
}

function unlink($filename, $context = null)
{
    return true;
}

function tempnam($directory, $prefix)
{
    global $shopFeatureLogoPath;

    return $shopFeatureLogoPath;
}

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Configuration;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Context\ShopContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\UploadLogosCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopAssociationNotFound;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;

class ShopFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * Random integer that represents shop id which should never exist in test database
     */
    private const NON_EXISTING_SHOP_ID = 77701;

    /**
     * @When I upload :path as new Header Logo
     */
    public function uploadHeaderLogo(string $path): void
    {
        global $shopFeatureLogoPath;
        $shopFeatureLogoPath = __DIR__ . '/../../../../../../' . $path;
        $uploadCommand = new UploadLogosCommand();
        $uploadCommand->setUploadedHeaderLogo(new UploadedFile($shopFeatureLogoPath, 'logo.jpg', null, null, true));
        $this->getCommandBus()->handle($uploadCommand);
    }

    /**
     * @Then the logo size configuration should be :width x :height
     *
     * @param int $width
     * @param int $height
     */
    public function logoSizeConfigurationShouldbe(int $width, int $height): void
    {
        $confWidth = (int) Configuration::get('SHOP_LOGO_WIDTH');
        $confHeight = (int) Configuration::get('SHOP_LOGO_HEIGHT');

        if ($confWidth !== $width) {
            throw new RuntimeException('Width does not match');
        }
        if ($confHeight !== $height) {
            throw new RuntimeException('Height does not match');
        }
    }

    /**
     * @Given shop :reference does not exist
     *
     * @param string $reference
     */
    public function setNonExistingShopReference(string $reference): void
    {
        if ($this->getSharedStorage()->exists($reference) && $this->getSharedStorage()->get($reference)) {
            throw new RuntimeException(sprintf('Expected that shop "%s" should not exist', $reference));
        }

        $this->getSharedStorage()->set($reference, self::NON_EXISTING_SHOP_ID);
    }

    /**
     * @Then I should get error that shop was not found
     */
    public function assertShopNotFound(): void
    {
        $this->assertLastErrorIs(ShopNotFoundException::class);
    }

    /**
     * @Then I should get error that shop association was not found
     */
    public function assertLastErrorIsShopAssociationNotFound(): void
    {
        $this->assertLastErrorIs(ShopAssociationNotFound::class);
    }

    /**
     * @Given I set up shop context to single shop :shopReference
     */
    public function setupShopContext(string $shopReference)
    {
        // We only need to update the builder settings, ShopContext service was defined as NOT shared
        // so each time it is accessed a new instance is created and the builder is called again
        /** @var ShopContextBuilder $shopContextBuilder */
        $shopContextBuilder = CommonFeatureContext::getContainer()->get('test_shop_context_builder');
        $shopContextBuilder->setShopConstraint(ShopConstraint::shop($this->referenceToId($shopReference)));
        $shopContextBuilder->setShopId($this->referenceToId($shopReference));
    }
}
