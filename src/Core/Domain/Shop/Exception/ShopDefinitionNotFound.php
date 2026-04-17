<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\Exception;

/**
 * Thrown when trying to use a multishop feature on an entity that has no multishop feature
 * (no association and no multilang_shop feature).
 */
class ShopDefinitionNotFound extends ShopException
{
}
