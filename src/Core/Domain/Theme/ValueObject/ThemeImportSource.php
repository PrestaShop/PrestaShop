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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\NotSupportedThemeImportSourceException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ThemeImportSource defines available sources from where theme can be imported.
 */
class ThemeImportSource
{
    const FROM_ARCHIVE = 'from_archive';
    const FROM_WEB = 'from_web';
    const FROM_FTP = 'from_ftp';

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
