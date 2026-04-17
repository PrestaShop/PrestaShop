<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Combination;

/**
 * Defines settings for combination.
 * If related Value Object does not exist, then various settings (e.g. regex, length constraints) are saved here
 */
class CombinationSettings
{
    /**
     * Class not supposed to be initialized, it only serves as static storage
     */
    private function __construct()
    {
    }

    /**
     * Bellow constants define maximum allowed length of combination properties
     */
    public const MAX_AVAILABLE_NOW_LABEL_LENGTH = 255;
    public const MAX_AVAILABLE_LATER_LABEL_LENGTH = 255;
}
