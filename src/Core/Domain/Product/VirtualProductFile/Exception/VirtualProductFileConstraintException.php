<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\VirtualProductFile\Exception;

/**
 * Thrown when virtual product file constraints are violated
 * Each constant represents related error code
 */
class VirtualProductFileConstraintException extends VirtualProductFileException
{
    /**
     * Each of following constants respectively represents invalid entity properties
     */
    public const INVALID_ID = 10;
    public const INVALID_DISPLAY_NAME = 20;
    public const INVALID_FILENAME = 30;
    public const INVALID_CREATION_DATE = 40;
    public const INVALID_EXPIRATION_DATE = 50;
    public const INVALID_ACCESS_DAYS = 60;
    public const INVALID_DOWNLOAD_TIMES_LIMIT = 70;
    public const INVALID_ACTIVE = 80;
    public const INVALID_SHAREABLE = 90;

    /** Is thrown when file already exists for given product */
    public const ALREADY_HAS_A_FILE = 100;
}
