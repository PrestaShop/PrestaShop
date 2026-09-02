<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestSettingsConstraintException;
use PrestaShop\PrestaShop\Core\Encoding\CharsetEncoding;

/**
 * Class SaveSqlManagerSettingsCommand saves default file encoding settings
 * for SqlRequest's query result export file.
 */
class SaveSqlRequestSettingsCommand
{
    private string $fileEncoding;
    private string $fileSeparator;

    /**
     * @param string $fileEncoding
     * @param string $fileSeparator
     *
     * @throws SqlRequestSettingsConstraintException
     */
    public function __construct(string $fileEncoding, string $fileSeparator)
    {
        $this->setFileEncoding($fileEncoding);
        $this->setFileSeparator($fileSeparator);
    }

    /**
     * @return string
     */
    public function getFileEncoding(): string
    {
        return $this->fileEncoding;
    }

    /**
     * @return string
     */
    public function getFileSeparator(): string
    {
        return $this->fileSeparator;
    }

    /**
     * @param string $fileEncoding
     *
     * @return void
     *
     * @throws SqlRequestSettingsConstraintException
     */
    private function setFileEncoding(string $fileEncoding): void
    {
        if (empty($fileEncoding)) {
            throw new SqlRequestSettingsConstraintException(
                sprintf('Invalid File Encoding %s supplied', var_export($fileEncoding, true)),
                SqlRequestSettingsConstraintException::INVALID_FILE_ENCODING
            );
        }

        $supportedFileEncodings = [
            CharsetEncoding::ISO_8859_1,
            CharsetEncoding::UTF_8,
        ];

        if (!in_array($fileEncoding, $supportedFileEncodings, true)) {
            throw new SqlRequestSettingsConstraintException(
                sprintf(
                    'Not supported File Encoding %s supplied. Supported encodings are %s',
                    var_export($fileEncoding, true),
                    var_export(implode(',', $supportedFileEncodings), true)
                ),
                SqlRequestSettingsConstraintException::NOT_SUPPORTED_FILE_ENCODING
            );
        }

        $this->fileEncoding = $fileEncoding;
    }

    /**
     * @param string $fileSeparator
     *
     * @return void
     *
     * @throws SqlRequestSettingsConstraintException
     */
    private function setFileSeparator(string $fileSeparator): void
    {
        if (empty($fileSeparator)) {
            throw new SqlRequestSettingsConstraintException(
                sprintf('Invalid File Separator %s supplied', var_export($fileSeparator, true)),
                SqlRequestSettingsConstraintException::INVALID_FILE_SEPARATOR
            );
        }

        $this->fileSeparator = $fileSeparator;
    }
}
