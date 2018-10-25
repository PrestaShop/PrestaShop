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

namespace PrestaShop\PrestaShop\Core\Import\Configuration;

/**
 * Interface ImportConfigInterface describes an import configuration VO.
 */
interface ImportConfigInterface
{
    /**
     * Get the import file name.
     *
     * @return string
     */
    public function getFileName();

    /**
     * Get the import entity type.
     * @see constants defined in \PrestaShop\PrestaShop\Core\Import\Entity for available types.
     *
     * @return int
     */
    public function getEntityType();

    /**
     * Get import language ISO code.
     *
     * @return string
     */
    public function getLanguageIso();

    /**
     * Get import file's separator.
     *
     * @return string
     */
    public function getSeparator();

    /**
     * Get import file's multiple value separator
     *
     * @return string
     */
    public function getMultipleValueSeparator();

    /**
     * Should the entity data be truncated before import.
     *
     * @return bool
     */
    public function truncate();

    /**
     * Should skip the thumbnail regeneration after import.
     *
     * @return bool
     */
    public function skipThumbnailRegeneration();

    /**
     * Should the product reference be used as import primary key.
     *
     * @return bool
     */
    public function matchReferences();

    /**
     * Should the IDs from import file be used as-is.
     *
     * @return bool
     */
    public function forceIds();

    /**
     * Should the system send a confirmation email when the import operation completes.
     *
     * @return bool
     */
    public function sendEmail();
}
