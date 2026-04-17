<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\ShowcaseCard\Query\GetShowcaseCardIsClosed;

/**
 * Contract for handling GetShowcaseCardIsClosed
 */
interface GetShowcaseCardIsClosedHandlerInterface
{
    /**
     * Returns the "closed state" of a showcase command
     *
     * @param GetShowcaseCardIsClosed $query
     *
     * @return bool True if the showcase card is closed, False otherwise
     */
    public function handle(GetShowcaseCardIsClosed $query);
}
