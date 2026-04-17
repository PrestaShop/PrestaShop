<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
