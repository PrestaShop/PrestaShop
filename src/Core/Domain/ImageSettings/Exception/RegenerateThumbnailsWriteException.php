<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception;

/**
 * Is thrown when a write access fail occurs during thumbnails regeneration
 */
class RegenerateThumbnailsWriteException extends RegenerateThumbnailsException
{
}
