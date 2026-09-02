<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\NotSupportedThemeImportSourceException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ThemeImportSource defines available sources from where theme can be imported.
 */
class ThemeImportSource
{
    public const FROM_ARCHIVE = 'from_archive';
    public const FROM_WEB = 'from_web';
    public const FROM_FTP = 'from_ftp';

    /**
     * @var string
     */
    private $sourceType;

    /**
     * @var UploadedFile|string If import source type is "from archive"
     *                          then $source is uploaded file or path to theme otherwise
     */
    private $source;

    /**
     * @param UploadedFile $uploadedTheme
     *
     * @return ThemeImportSource
     */
    public static function fromArchive(UploadedFile $uploadedTheme)
    {
        return new self(self::FROM_ARCHIVE, $uploadedTheme);
    }

    /**
     * @param string $themeUrl
     *
     * @return ThemeImportSource
     */
    public static function fromWeb($themeUrl)
    {
        return new self(self::FROM_WEB, $themeUrl);
    }

    /**
     * @param string $themeFtp
     *
     * @return ThemeImportSource
     */
    public static function fromFtp($themeFtp)
    {
        return new self(self::FROM_FTP, $themeFtp);
    }

    /**
     * @param string $sourceType
     * @param UploadedFile|string $source
     *
     * @throws NotSupportedThemeImportSourceException
     */
    private function __construct($sourceType, $source)
    {
        $this->assertSupportedThemeImportSourceTypeSupplied($sourceType);

        $this->sourceType = $sourceType;
        $this->source = $source;
    }

    /**
     * @return string
     */
    public function getSourceType()
    {
        return $this->sourceType;
    }

    /**
     * @return string|UploadedFile
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param string $sourceType
     *
     * @throws NotSupportedThemeImportSourceException
     */
    private function assertSupportedThemeImportSourceTypeSupplied($sourceType)
    {
        $supportedSources = [self::FROM_ARCHIVE, self::FROM_WEB, self::FROM_FTP];

        if (!in_array($sourceType, $supportedSources)) {
            throw new NotSupportedThemeImportSourceException(sprintf('Not supported %s theme import source type supplied. Supported sources are: "%s"', var_export($sourceType, true), implode(',', $supportedSources)));
        }
    }
}
