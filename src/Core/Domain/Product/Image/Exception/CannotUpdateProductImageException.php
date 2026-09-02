<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception;

class CannotUpdateProductImageException extends ProductImageException
{
    /**
     * When fails to update cover
     */
    public const FAILED_UPDATE_COVER = 10;

    /**
     * When fails to update legends
     */
    public const FAILED_UPDATE_LEGENDS = 20;

    /**
     * When fails to update position
     */
    public const FAILED_UPDATE_POSITION = 30;
}
