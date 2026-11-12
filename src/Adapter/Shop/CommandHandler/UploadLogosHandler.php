<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Shop\CommandHandler;

use Configuration as ConfigurationLegacy;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Exception\FileUploadException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\UploadLogosCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler\UploadLogosHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\DTO\ShopLogoSettings;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Shop\LogoUploader;
use PrestaShopException;
use Shop;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UploadLogosHandler
 */
#[AsCommandHandler]
final class UploadLogosHandler implements UploadLogosHandlerInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var LogoUploader
     */
    private $logoUploader;

    /**
     * @var HookDispatcherInterface
     */
    private $hookDispatcher;

    /**
     * @param ConfigurationInterface $configuration
     * @param LogoUploader $logoUploader
     * @param HookDispatcherInterface $hookDispatcher
     */
    public function __construct(
        ConfigurationInterface $configuration,
        LogoUploader $logoUploader,
        HookDispatcherInterface $hookDispatcher
    ) {
        $this->configuration = $configuration;
        $this->logoUploader = $logoUploader;
        $this->hookDispatcher = $hookDispatcher;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ShopException
     * @throws FileUploadException
     */
    public function handle(UploadLogosCommand $command)
    {
        $this->configuration->set('PS_IMG_UPDATE_TIME', time());

        try {
            if (null !== $command->getUploadedHeaderLogo()) {
                $this->uploadHeaderLogo($command->getUploadedHeaderLogo());
            }

            if (null !== $command->getUploadedMailLogo()) {
                $this->uploadMailLogo($command->getUploadedMailLogo());
            }

            if (null !== $command->getUploadedInvoiceLogo()) {
                $this->uploadInvoiceLogo($command->getUploadedInvoiceLogo());
            }

            if (null !== $command->getUploadedFavicon()) {
                $this->uploadFavicon($command->getUploadedFavicon());
            }
        } catch (PrestaShopException $exception) {
            throw new ShopException('An unexpected error occurred when uploading image', 0, $exception);
        }

        $this->hookDispatcher->dispatchWithParameters('actionAdminThemesControllerUpdate_optionsAfter');
    }

    /**
     * @param UploadedFile $uploadedFile
     */
    private function uploadHeaderLogo(UploadedFile $uploadedFile)
    {
        $this->setUploadedFileToBeCompatibleWithLegacyUploader(ShopLogoSettings::HEADER_LOGO_FILE_NAME, $uploadedFile);

        $this->logoUploader->updateHeader();
        $this->dropShopOverridesOfGroupContext(['PS_LOGO', 'SHOP_LOGO_HEIGHT', 'SHOP_LOGO_WIDTH']);
    }

    /**
     * @param UploadedFile $uploadedFile
     */
    private function uploadMailLogo(UploadedFile $uploadedFile)
    {
        $this->setUploadedFileToBeCompatibleWithLegacyUploader(ShopLogoSettings::MAIL_LOGO_FILE_NAME, $uploadedFile);

        $this->logoUploader->updateMail();
        $this->dropShopOverridesOfGroupContext(['PS_LOGO_MAIL']);
    }

    /**
     * @param UploadedFile $uploadedHeaderLogo
     */
    private function uploadInvoiceLogo(UploadedFile $uploadedHeaderLogo)
    {
        $this->setUploadedFileToBeCompatibleWithLegacyUploader(ShopLogoSettings::INVOICE_LOGO_FILE_NAME, $uploadedHeaderLogo);

        $this->logoUploader->updateInvoice();
        $this->dropShopOverridesOfGroupContext(['PS_LOGO_INVOICE']);
    }

    /**
     * @param UploadedFile $uploadedHeaderLogo
     */
    private function uploadFavicon(UploadedFile $uploadedHeaderLogo)
    {
        $this->setUploadedFileToBeCompatibleWithLegacyUploader(ShopLogoSettings::FAVICON_FILE_NAME, $uploadedHeaderLogo);

        $this->logoUploader->updateFavicon();
    }

    /**
     * A logo saved from a group context belongs to every shop of that group. The value is stored on the
     * group, but a value stored earlier for one of those shops individually keeps taking precedence over
     * it, so the new logo appears only on the shops that never had one of their own. Dropping those
     * leaves the group value as the one they all read.
     *
     * The favicon is deliberately left out: updateFavicon() names the file after the shop and stores it
     * per shop on purpose.
     *
     * @param string[] $configurationKeys
     */
    private function dropShopOverridesOfGroupContext(array $configurationKeys): void
    {
        if (!Shop::isFeatureActive() || Shop::getContext() !== Shop::CONTEXT_GROUP) {
            return;
        }

        $idShopGroup = (int) Shop::getContextShopGroupID();
        foreach (Shop::getShops(false, $idShopGroup, true) as $idShop) {
            foreach ($configurationKeys as $configurationKey) {
                ConfigurationLegacy::deleteFromGivenContext($configurationKey, $idShopGroup, (int) $idShop);
            }
        }
    }

    /**
     * @param string $legacyFileName
     * @param UploadedFile $uploadedFile
     *
     * @return array
     */
    private function setUploadedFileToBeCompatibleWithLegacyUploader($legacyFileName, UploadedFile $uploadedFile)
    {
        $_FILES[$legacyFileName] = [
            'name' => $uploadedFile->getClientOriginalName(),
            'type' => $uploadedFile->getMimeType(),
            'tmp_name' => $uploadedFile->getPathname(),
            'error' => $uploadedFile->getError(),
            'size' => $uploadedFile->getSize(),
        ];

        return $_FILES;
    }
}
