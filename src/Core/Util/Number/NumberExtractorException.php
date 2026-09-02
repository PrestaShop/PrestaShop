<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Util\Number;

use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Exception thrown when something goes wrong in @var NumberExtractor service
 */
class NumberExtractorException extends CoreException
{
    /**
     * When provided property path is not valid
     */
    public const INVALID_PROPERTY_PATH = 10;

    /**
     * When the resource/property from which value is being extracted is of invalid type
     */
    public const INVALID_RESOURCE_TYPE = 20;

    /**
     * When property is not accessible or doesn't exist
     */
    public const NOT_ACCESSIBLE = 30;

    /**
     * When property type is not numeric therefore it cannot be converted to number
     */
    public const NON_NUMERIC_PROPERTY = 40;
}
