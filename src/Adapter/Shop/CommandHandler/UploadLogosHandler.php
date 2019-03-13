<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Shop\CommandHandler;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\UploadLogosCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\CommandHandler\UploadLogosHandlerInterface;
use PrestaShop\PrestaShop\Core\Form\ValueObject\ShopRestriction;
use PrestaShop\PrestaShop\Core\Hook\HookDispatcherInterface;
use PrestaShop\PrestaShop\Core\Shop\LogoUploader;
use Shop;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Validate;

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
     */
    public function handle(UploadLogosCommand $command)
    {
        $this->configuration->set('PS_IMG_UPDATE_TIME', time());

        if (null !== $command->getUploadedHeaderLogo() &&
            $this->isAllowedByShopRestriction('header_logo', $command->getShopRestriction())
        ) {
            $this->uploadHeaderLogo($command->getUploadedHeaderLogo());
        }

        if (null !== $command->getUploadedMailLogo() &&
            $this->isAllowedByShopRestriction('mail_logo', $command->getShopRestriction())
        ) {
            $this->uploadMailLogo($command->getUploadedMailLogo());
        }

        if (null !== $command->getUploadedInvoiceLogo() &&
            $this->isAllowedByShopRestriction('invoice_logo', $command->getShopRestriction())
        ) {
            $this->uploadInvoiceLogo($command->getUploadedInvoiceLogo());
        }

        if (null !== $command->getUploadedFavicon() &&
            $this->isAllowedByShopRestriction('favicon', $command->getShopRestriction())
        ) {
            $this->uploadFavicon($command->getUploadedFavicon());
        }

        $this->hookDispatcher->dispatchWithParameters('actionAdminThemesControllerUpdate_optionsAfter');
    }

    /**
     * @param UploadedFile $uploadedFile
     */
    private function uploadHeaderLogo(UploadedFile $uploadedFile)
    {
        $this->setUploadedFileToBeCompatibleWithLegacyUploader('PS_LOGO', $uploadedFile);
        $this->logoUploader->updateHeader();
    }

    /**
     * @param UploadedFile $uploadedFile
     */
    private function uploadMailLogo(UploadedFile $uploadedFile)
    {
        $this->setUploadedFileToBeCompatibleWithLegacyUploader('PS_LOGO_MAIL', $uploadedFile);

        $this->logoUploader->updateMail();
    }

    /**
     * @param UploadedFile $uploadedHeaderLogo
     */
    private function uploadInvoiceLogo(UploadedFile $uploadedHeaderLogo)
    {
        $this->setUploadedFileToBeCompatibleWithLegacyUploader('PS_LOGO_INVOICE', $uploadedHeaderLogo);

        $this->logoUploader->updateInvoice();
    }

    /**
     * @param UploadedFile $uploadedHeaderLogo
     */
    private function uploadFavicon(UploadedFile $uploadedHeaderLogo)
    {
        $this->setUploadedFileToBeCompatibleWithLegacyUploader('PS_FAVICON', $uploadedHeaderLogo);

        $this->logoUploader->updateFavicon();
    }

    /**
     * @param string $legacyFileName
     * @param UploadedFile $uploadedFile
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
    }

    /**
     * @param string $formFieldName
     *
     * @param ShopRestriction|null $shopRestriction
     *
     * @return bool
     */
    private function isAllowedByShopRestriction($formFieldName, $shopRestriction)
    {
        if (null === $shopRestriction) {
            return true;
        }

        $shopRestrictionFields = $shopRestriction->getShopRestrictionFields();

        foreach ($shopRestrictionFields as $shopRestrictionField) {
            if ($formFieldName === $shopRestrictionField->getFieldName() &&
                $shopRestrictionField->isRestrictedToContextShop()
            ) {
                return true;
            }
        }

        return false;
    }
}
