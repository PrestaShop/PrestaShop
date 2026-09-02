<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Image\Exception;

use Exception;

/**
 * Class AvifUnavailableException used when AVIF seems to be available, but in fact, not.
 *
 * @see https://stackoverflow.com/questions/71739530/php-8-1-imageavif-avif-image-support-has-been-disabled
 */
class AvifUnavailableException extends Exception
{
}
