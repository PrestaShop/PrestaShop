<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Shop\CommandHandler;

use ImageManager;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Exception\MaximumFileSizeBreachedException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\UploadLogosCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler\UploadLogosHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\DTO\ShopLogoSettings;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\ShopException;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Shop\LogoUploader;
use PrestaShopException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tools;

/**
 * Class UploadLogosHandler
 */
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
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ConfigurationInterface $configuration
     * @param LogoUploader $logoUploader
     * @param HookDispatcherInterface $hookDispatcher
     * @param ValidatorInterface $validator
     */
    public function __construct(
        ConfigurationInterface $configuration,
        LogoUploader $logoUploader,
        HookDispatcherInterface $hookDispatcher,
        ValidatorInterface $validator
    ) {
        $this->configuration = $configuration;
        $this->logoUploader = $logoUploader;
        $this->hookDispatcher = $hookDispatcher;
        $this->validator = $validator;
    }

    /**
     * {@inheritdoc}
     *
     * @throws ShopException
     * @throws MaximumFileSizeBreachedException
     */
    public function handle(UploadLogosCommand $command)
    {
        $this->configuration->set('PS_IMG_UPDATE_TIME', time());

        try {
            if (null !== $command->getUploadedHeaderLogo()) {
                $this->assertIsMaxFileSizeNotBreached($command->getUploadedHeaderLogo());
                $this->uploadHeaderLogo($command->getUploadedHeaderLogo());
            }

            if (null !== $command->getUploadedMailLogo()) {
                $this->assertIsMaxFileSizeNotBreached($command->getUploadedMailLogo());
                $this->uploadMailLogo($command->getUploadedMailLogo());
            }

            if (null !== $command->getUploadedInvoiceLogo()) {
                $this->assertIsMaxFileSizeNotBreached($command->getUploadedInvoiceLogo());
                $this->uploadInvoiceLogo($command->getUploadedInvoiceLogo());
            }

            if (null !== $command->getUploadedFavicon()) {
                $this->assertIsMaxFileSizeNotBreached($command->getUploadedFavicon());
                $this->uploadFavicon($command->getUploadedFavicon());
            }
        } catch (PrestaShopException $exception) {
            throw new ShopException(
                'An unexpected error occurred when uploading image',
                0,
                $exception
            );
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
    }

    /**
     * @param UploadedFile $uploadedFile
     */
    private function uploadMailLogo(UploadedFile $uploadedFile)
    {
        $this->setUploadedFileToBeCompatibleWithLegacyUploader(ShopLogoSettings::MAIL_LOGO_FILE_NAME, $uploadedFile);

        $this->logoUploader->updateMail();
    }

    /**
     * @param UploadedFile $uploadedHeaderLogo
     */
    private function uploadInvoiceLogo(UploadedFile $uploadedHeaderLogo)
    {
        $this->setUploadedFileToBeCompatibleWithLegacyUploader(ShopLogoSettings::INVOICE_LOGO_FILE_NAME, $uploadedHeaderLogo);

        $this->logoUploader->updateInvoice();
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

    /**
     * @param UploadedFile $uploadedFile
     *
     * @throws MaximumFileSizeBreachedException
     */
    private function assertIsMaxFileSizeNotBreached(UploadedFile $uploadedFile)
    {
        $maxSizeInBytes = Tools::getMaxUploadSize();

        $errors = $this->validator->validate(
            $uploadedFile,
            new File([
                'maxSize' => $maxSizeInBytes,
            ])
        );

        if (0 !== count($errors)) {
            throw new MaximumFileSizeBreachedException(
                $uploadedFile->getSize(),
                $maxSizeInBytes,
                sprintf(
                    'An error occurred when uploading file %s : max size of %s bytes breached. Current file size is %s bytes',
                    $uploadedFile->getFilename(),
                    $maxSizeInBytes,
                    $uploadedFile->getSize()
                )
            );
        }
    }
}
