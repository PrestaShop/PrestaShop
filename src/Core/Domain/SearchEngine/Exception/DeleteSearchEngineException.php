<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\SearchEngine\Exception;

/**
 * Is thrown when search engine(s) cannot be deleted.
 */
class DeleteSearchEngineException extends SearchEngineException
{
    /**
     * When fails to delete single search engine.
     */
    public const FAILED_DELETE = 10;

    /**
     * When fails to delete search engines in bulk action.
     */
    public const FAILED_BULK_DELETE = 20;
}
