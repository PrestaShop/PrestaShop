<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Transform;

use Behat\Behat\Context\Context;

/**
 * Contains string to boolean transformations
 */
class StringToBoolTransformContext implements Context
{
    /**
     * @Transform /^(enabled|enable|included|should|includes)$/
     *
     * @param string $string
     *
     * @return bool
     */
    public function transformTruthyStringToBoolean(string $string): bool
    {
        return true;
    }

    /**
     * @Transform /^(disabled|disable|excluded|should not|excludes)$/
     *
     * @param string $string
     *
     * @return bool
     */
    public function transformFalsyStringToBoolean(string $string): bool
    {
        return false;
    }
}
