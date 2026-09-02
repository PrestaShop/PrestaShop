<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Category\Exception;

/**
 * Class CannotEditRootCategoryException is thrown when trying to edit the ROOT category.
 */
class CannotEditRootCategoryException extends CategoryException
{
}
