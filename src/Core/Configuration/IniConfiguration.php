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

namespace  PrestaShop\PrestaShop\Core\Configuration;

/**
 * Gets ini configuration.
 */
class IniConfiguration
{
    /**
     * Get max post max size from ini configuration in bytes.
     *
     * @return int
     */
    public function getPostMaxSizeInBytes()
    {
        return $this->convertToBytes(ini_get('post_max_size'));
    }

    /**
     * Get maximum upload size allowed by the server in bytes.
     *
     * @return int
     */
    public function getUploadMaxSizeInBytes()
    {
        return min(
            $this->convertToBytes(ini_get('upload_max_filesize')),
            $this->getPostMaxSizeInBytes()
        );
    }

    /**
     * Convert a numeric value to bytes.
     *
     * @param int $value
     *
     * @return int
     */
    private function convertToBytes($value)
    {
        $bytes = (int) trim($value);
        $last = strtolower($value[strlen($value) - 1]);

        switch ($last) {
            case 'g':
                $bytes *= 1024;
            // no break to fall-through
            case 'm':
                $bytes *= 1024;
            // no break to fall-through
            case 'k':
                $bytes *= 1024;
        }

        return $bytes;
    }
}
