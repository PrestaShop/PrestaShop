<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Transform;

use Behat\Behat\Context\Context;
use Tests\Integration\Behaviour\Features\Context\Util\PrimitiveUtils;

/**
 * Contains string to array transformations
 */
class StringToArrayTransformContext implements Context
{
    /**
     * @Transform /^\[.*?\]$/
     *
     * @param string $string
     *
     * @return array
     */
    public function transformStringArrayToArray(string $string): array
    {
        return PrimitiveUtils::castStringArrayIntoArray($string);
    }
}
