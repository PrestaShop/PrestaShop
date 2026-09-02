<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement;

/**
 * Class SqlRequestSettings stores SqlRequest settings.
 */
class SqlRequestSettings
{
    /**
     * Name of the setting for SqlRequest SQL query result file encoding in ps_configuration.
     */
    public const FILE_ENCODING = 'PS_ENCODING_FILE_MANAGER_SQL';

    /**
     * Name of the setting for SqlRequest SQL query result file separator in configuration.
     */
    public const FILE_SEPARATOR = 'PS_SEPARATOR_FILE_MANAGER_SQL';

    /**
     * @var string Encoding in which downloaded SqlRequest SQL query result files will be encoded
     */
    private string $fileEncoding;

    /**
     * @var string Separator used in downloaded SqlRequest SQL query result files
     */
    private string $fileSeparator;

    /**
     * @param string $fileEncoding
     * @param string $fileSeparator
     */
    public function __construct(string $fileEncoding, string $fileSeparator)
    {
        $this->fileEncoding = $fileEncoding;
        $this->fileSeparator = $fileSeparator;
    }

    /**
     * @return string
     */
    public function getFileEncoding(): string
    {
        return $this->fileEncoding;
    }

    public function getFileSeparator(): string
    {
        return $this->fileSeparator;
    }
}
