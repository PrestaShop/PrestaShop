<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Import\Query\GetImportRunState;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportRunState;

/**
 * Defines the contract for handling @see GetImportRunState.
 */
interface GetImportRunStateHandlerInterface
{
    public function handle(GetImportRunState $query): ImportRunState;
}
