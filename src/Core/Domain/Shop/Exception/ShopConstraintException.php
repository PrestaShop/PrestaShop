<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Exception;

class ShopConstraintException extends ShopException
{
    public const INVALID_NAME = 1;
    public const INVALID_COLOR = 2;
    public const INVALID_THEME = 3;
    public const NAME_ALREADY_USED_IN_GROUP = 4;
    public const INVALID_ROOT_CATEGORY = 5;
    public const ROOT_CATEGORY_NOT_ASSOCIATED = 6;
}
