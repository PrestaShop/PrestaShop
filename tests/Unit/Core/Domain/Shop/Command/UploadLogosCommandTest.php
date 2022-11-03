<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\Shop\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Exception\FileUploadException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Command\UploadLogosCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\NotSupportedLogoImageExtensionException;
use PrestaShop\PrestaShop\Core\Domain\Shop\Exception\NotSupportedMailAndInvoiceImageExtensionException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadLogosCommandTest extends TestCase
{
    /**
     * @var string
     */
    private $validImagePath = '';

    public function setUp(): void
    {
        parent::setUp();
        $this->validImagePath = _PS_ROOT_DIR_ . '/tests/Unit/Resources/assets/img/logo.jpg';
    }

    public function testSetUploadedHeaderLogoNotValidHeaderLogoImage(): void
    {
        $this->expectException(NotSupportedLogoImageExtensionException::class);
        $this->expectExceptionMessage('Not supported "php" image logo extension. Supported extensions are "gif,jpg,jpeg,jpe,png,webp,svg"');

        $uploadLogosCommand = new UploadLogosCommand();
        $uploadedFile = new UploadedFile(__FILE__, basename(__FILE__));
        $uploadLogosCommand->setUploadedHeaderLogo($uploadedFile);
    }

    public function testSetUploadedMailLogoNotValidHeaderLogoImage(): void
    {
        $this->expectException(NotSupportedMailAndInvoiceImageExtensionException::class);
        $this->expectExceptionMessage('Not supported "php" image logo extension. Supported extensions are "gif,jpg,jpeg,jpe,png,webp"');

        $uploadLogosCommand = new UploadLogosCommand();
        $uploadedFile = new UploadedFile(__FILE__, basename(__FILE__));
        $uploadLogosCommand->setUploadedMailLogo($uploadedFile);
    }

    public function testSetUploadedInvoiceLogoNotValidHeaderLogoImage(): void
    {
        $this->expectException(NotSupportedMailAndInvoiceImageExtensionException::class);
        $this->expectExceptionMessage('Not supported "php" image logo extension. Supported extensions are "gif,jpg,jpeg,jpe,png,webp"');

        $uploadLogosCommand = new UploadLogosCommand();
        $uploadedFile = new UploadedFile(__FILE__, basename(__FILE__));
        $uploadLogosCommand->setUploadedInvoiceLogo($uploadedFile);
    }

    public function testSetUploadedHeaderLogoNativeFileValidationDoesFail(): void
    {
        $this->expectException(FileUploadException::class);
        $this->expectExceptionMessage('The file "logo.jpg" could not be written on disk.');

        $uploadLogosCommand = new UploadLogosCommand();
        $uploadedFile = new UploadedFile($this->validImagePath, basename($this->validImagePath), null, UPLOAD_ERR_CANT_WRITE);
        $uploadLogosCommand->setUploadedHeaderLogo($uploadedFile);
    }

    /**
     * @dataProvider dataProviderSetUploadedHeaderLogo
     *
     * @param string $path
     */
    public function testSetUploadedHeaderLogo(string $path): void
    {
        $uploadLogosCommand = new UploadLogosCommand();
        $uploadedFile = new UploadedFile($path, basename($path));
        $uploadLogosCommand->setUploadedHeaderLogo($uploadedFile);

        self::assertSame(
            $uploadedFile,
            $uploadLogosCommand->getUploadedHeaderLogo()
        );
    }

    /**
     * @dataProvider dataProviderSetUploadedMailAndInvoiceLogo
     *
     * @param string $path
     */
    public function testSetUploadedMailLogo(string $path): void
    {
        $uploadLogosCommand = new UploadLogosCommand();
        $uploadedFile = new UploadedFile($path, basename($path));
        $uploadLogosCommand->setUploadedMailLogo($uploadedFile);

        self::assertSame(
            $uploadedFile,
            $uploadLogosCommand->getUploadedMailLogo()
        );
    }

    /**
     * @dataProvider dataProviderSetUploadedMailAndInvoiceLogo
     *
     * @param string $path
     */
    public function testSetUploadedInvoiceLogo(string $path): void
    {
        $uploadLogosCommand = new UploadLogosCommand();
        $uploadedFile = new UploadedFile($path, basename($path));
        $uploadLogosCommand->setUploadedInvoiceLogo($uploadedFile);

        self::assertSame(
            $uploadedFile,
            $uploadLogosCommand->getUploadedInvoiceLogo()
        );
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function dataProviderSetUploadedHeaderLogo(): array
    {
        return [
            [_PS_ROOT_DIR_ . '/tests/Unit/Resources/assets/img/logo.gif'],
            [_PS_ROOT_DIR_ . '/tests/Unit/Resources/assets/img/logo.jpe'],
            [_PS_ROOT_DIR_ . '/tests/Unit/Resources/assets/img/logo.jpg'],
            [_PS_ROOT_DIR_ . '/tests/Unit/Resources/assets/img/logo.jpeg'],
            [_PS_ROOT_DIR_ . '/tests/Unit/Resources/assets/img/logo.png'],
            [_PS_ROOT_DIR_ . '/tests/Unit/Resources/assets/img/logo.webp'],
            [_PS_ROOT_DIR_ . '/tests/Unit/Resources/assets/img/logo.svg'],
        ];
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function dataProviderSetUploadedMailAndInvoiceLogo(): array
    {
        return [
            [_PS_ROOT_DIR_ . '/tests/Unit/Resources/assets/img/logo.gif'],
            [_PS_ROOT_DIR_ . '/tests/Unit/Resources/assets/img/logo.jpe'],
            [_PS_ROOT_DIR_ . '/tests/Unit/Resources/assets/img/logo.jpg'],
            [_PS_ROOT_DIR_ . '/tests/Unit/Resources/assets/img/logo.jpeg'],
            [_PS_ROOT_DIR_ . '/tests/Unit/Resources/assets/img/logo.png'],
            [_PS_ROOT_DIR_ . '/tests/Unit/Resources/assets/img/logo.webp'],
        ];
    }
}
