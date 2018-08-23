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

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestSettingsConstraintException;
use PrestaShop\PrestaShop\Core\Encoding\CharsetEncoding;

/**
 * Class SaveDefaultFileEncodingSettingsCommand saves default file encoding settings
 * for SqlRequest's query result export file
 */
class SaveSqlRequestSettingsCommand
{
    /**
     * @var string
     */
    private $fileEncoding;

    /**
     * @param string $fileEncoding
     *
     * @throws SqlRequestSettingsConstraintException
     */
    public function __construct($fileEncoding)
    {
        $this->setFileEncoding($fileEncoding);
    }

    /**
     * @return string
     */
    public function getFileEncoding()
    {
        return $this->fileEncoding;
    }

    /**
     * @param string $fileEncoding
     *
     * @throws SqlRequestSettingsConstraintException
     */
    private function setFileEncoding($fileEncoding)
    {
        if (!is_string($fileEncoding) || empty($fileEncoding)) {
            throw new SqlRequestSettingsConstraintException(
                sprintf('Invalid File Encoding %s supplied', var_export($fileEncoding, true)),
                SqlRequestSettingsConstraintException::INVALID_FILE_ENCODING
            );
        }

        $supportedFileEncodings = [
            CharsetEncoding::ISO_8859_1,
            CharsetEncoding::UTF_8,
        ];

        if (!in_array($fileEncoding, $supportedFileEncodings)) {
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
}
