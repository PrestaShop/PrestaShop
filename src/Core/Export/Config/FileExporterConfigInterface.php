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

namespace PrestaShop\PrestaShop\Core\Export\Config;

/**
 * Interface FileExporterConfigInterface defines contract for FileExporterConfigInterface
 */
interface FileExporterConfigInterface
{
    /**
     * Gets file path which will be exported
     *
     * @return string
     */
    public function getFilePath();

    /**
     * Gets the name which represents the file to be exported
     *
     * @return string
     */
    public function getFileName();

    /**
     * Defined whenever the given file should be removed or not while export operation is being executed
     *
     * @return bool
     */
    public function useFileUnlink();
}
