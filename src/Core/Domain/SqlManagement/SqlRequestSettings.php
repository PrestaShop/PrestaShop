<?php
/**
 * 2007-2018 PrestaShop.
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

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement;

/**
 * Class SqlRequestSettings stores SqlRequest settings.
 */
class SqlRequestSettings
{
    /**
     * Name of the setting for SqlRequest SQL query result file encoding in ps_configuration.
     */
    const FILE_ENCODING = 'PS_ENCODING_FILE_MANAGER_SQL';

    /**
     * @var string Encoding in which downloaded SqlRequest SQL query result files will be encoded
     */
    private $fileEncoding;

    /**
     * @param $fileEncoding
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
     */
    private function setFileEncoding($fileEncoding)
    {
        $this->fileEncoding = $fileEncoding;
    }
}
