<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Category\Exception;

/**
 * Is thrown when trying to delete a root category for current shop context
 */
class CannotDeleteRootCategoryForShopException extends CategoryException
{
}
