<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Addon\Theme;

use PrestaShop\PrestaShop\Core\Addon\Theme\Exception\ThemeUploadException;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ThemeZipUploader uploads theme to local filesystem.
 */
final class ThemeZipUploader implements ThemeUploaderInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(UploadedFile $uploadedTheme)
    {
        $this->assertThemeWasUploadedWithoutErrors($uploadedTheme);
        $this->assertUploadedFileIsZip($uploadedTheme);

        $themesDir = $this->configuration->get('_PS_ALL_THEMES_DIR_');
        $destination = $themesDir . $uploadedTheme->getClientOriginalName();

        if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $uploadedTheme->getClientOriginalName())) {
            $destination = $themesDir . sha1_file($uploadedTheme->getPathname()) . '.zip';
        }

        move_uploaded_file(
            $uploadedTheme->getPathname(),
            $destination
        );

        return $destination;
    }

    /**
     * @param UploadedFile $uploadedTheme
     *
     * @throws ThemeUploadException
     */
    private function assertThemeWasUploadedWithoutErrors(UploadedFile $uploadedTheme)
    {
        if (UPLOAD_ERR_OK === $uploadedTheme->getError()) {
            return;
        }

        if (in_array($uploadedTheme->getError(), [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE])) {
            throw new ThemeUploadException('Allowed file size exceeded for uploaded theme.', ThemeUploadException::FILE_SIZE_EXCEEDED_ERROR);
        }

        throw new ThemeUploadException(sprintf('Unknown error "%s" occurred while uploading theme.', $uploadedTheme->getError()), ThemeUploadException::UNKNOWN_ERROR);
    }

    /**
     * @param UploadedFile $uploadedTheme
     *
     * @throws ThemeUploadException
     */
    private function assertUploadedFileIsZip(UploadedFile $uploadedTheme)
    {
        preg_match('#application/zip#', $uploadedTheme->getMimeType(), $matches);

        if (empty($matches)) {
            throw new ThemeUploadException('Invalid mime type of theme zip. Allowed mime type is application/zip.', ThemeUploadException::INVALID_MIME_TYPE);
        }
    }
}
