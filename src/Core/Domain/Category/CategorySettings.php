<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Category;

/**
 * Defines settings for category.
 * If related Value Object does not exist, then various settings (e.g. regex, length constraints) are saved here
 */
class CategorySettings
{
    /**
     * Class not supposed to be initialized, it only serves as static storage
     */
    private function __construct()
    {
    }

    /**
     * Bellow constants define maximum allowed length of category properties
     */
    public const MAX_TITLE_LENGTH = 128;
}
