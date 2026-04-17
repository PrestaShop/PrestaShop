<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Search\Exception;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainException;

/**
 * Thrown when search indexation fails.
 */
class SearchIndexationException extends DomainException
{
}
