<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
     *
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
     * Get import file's multiple value separator.
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

    /**
     * Get number of rows to skip from the beginning of import file.
     *
     * @return int
     */
    public function getNumberOfRowsToSkip();
}
