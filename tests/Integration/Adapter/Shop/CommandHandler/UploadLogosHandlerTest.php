<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Shop\CommandHandler;

use Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\CommandHandler\UploadLogosHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\UploadLogosCommand;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Shop\LogoUploader;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadLogosHandlerTest extends KernelTestCase
{
    private const KEY = 'PS_LOGO_MAIL';
    private const GROUP_VALUE = 'logo_mail-group.jpg';

    /**
     * @var int
     */
    private $secondShopId;

    /**
     * @var string
     */
    private $imagePath;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        Configuration::updateGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE', 1);
        Shop::setContext(Shop::CONTEXT_ALL);

        $shop = new Shop();
        $shop->name = 'UploadLogosHandlerTest shop';
        $shop->id_category = 2;
        $shop->id_shop_group = 1;
        $shop->active = true;
        $shop->add();
        $this->secondShopId = (int) $shop->id;

        Shop::resetContext();
        Shop::resetStaticCache();
        Configuration::loadConfiguration();

        $this->imagePath = sys_get_temp_dir() . '/upload-logos-handler-test.jpg';
        imagejpeg(imagecreatetruecolor(10, 10), $this->imagePath);
    }

    protected function tearDown(): void
    {
        (new Shop($this->secondShopId))->delete();
        Configuration::deleteByName(self::KEY);
        Shop::resetContext();
        Shop::resetStaticCache();
        Configuration::loadConfiguration();
        @unlink($this->imagePath);

        parent::tearDown();
    }

    /**
     * A logo saved from a group context is meant for the whole group, but a value stored earlier for one
     * of its shops keeps taking precedence, so the change reaches only the shops that never had one of
     * their own.
     */
    public function testAGroupContextUploadReachesTheShopsThatHaveTheirOwnLogo(): void
    {
        $this->givenEachShopHasItsOwnLogo();
        $this->givenTheGroupLogoIs(self::GROUP_VALUE);

        Shop::setContext(Shop::CONTEXT_GROUP, 1);
        Configuration::loadConfiguration();
        $this->buildHandler()->handle($this->mailLogoCommand());

        foreach ([1, $this->secondShopId] as $idShop) {
            Shop::setContext(Shop::CONTEXT_SHOP, $idShop);
            Configuration::loadConfiguration();
            $this->assertSame(
                self::GROUP_VALUE,
                Configuration::get(self::KEY),
                sprintf('Shop %d should read the logo saved for its group.', $idShop)
            );
        }
    }

    /**
     * Outside a group context each shop keeps the logo set for it.
     */
    public function testAShopContextUploadLeavesTheOtherShopsAlone(): void
    {
        $this->givenEachShopHasItsOwnLogo();

        Shop::setContext(Shop::CONTEXT_SHOP, 1);
        Configuration::loadConfiguration();
        $this->buildHandler()->handle($this->mailLogoCommand());

        Shop::setContext(Shop::CONTEXT_SHOP, $this->secondShopId);
        Configuration::loadConfiguration();
        $this->assertSame('own-logo-' . $this->secondShopId . '.jpg', Configuration::get(self::KEY));
    }

    private function givenEachShopHasItsOwnLogo(): void
    {
        foreach ([1, $this->secondShopId] as $idShop) {
            Shop::setContext(Shop::CONTEXT_SHOP, $idShop);
            Configuration::loadConfiguration();
            Configuration::updateValue(self::KEY, 'own-logo-' . $idShop . '.jpg');
        }
    }

    private function givenTheGroupLogoIs(string $value): void
    {
        Shop::setContext(Shop::CONTEXT_GROUP, 1);
        Configuration::loadConfiguration();
        Configuration::updateValue(self::KEY, $value);
    }

    private function mailLogoCommand(): UploadLogosCommand
    {
        $command = new UploadLogosCommand();
        $command->setUploadedMailLogo(new UploadedFile($this->imagePath, 'logo_mail.jpg', 'image/jpeg', null, true));

        return $command;
    }

    private function buildHandler(): UploadLogosHandler
    {
        return new UploadLogosHandler(
            $this->createMock(ConfigurationInterface::class),
            $this->createMock(LogoUploader::class),
            $this->createMock(HookDispatcherInterface::class)
        );
    }
}
