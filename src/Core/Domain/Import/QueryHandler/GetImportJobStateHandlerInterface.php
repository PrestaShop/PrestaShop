<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Import\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Import\Query\GetImportJobState;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportJobState;

/**
 * Defines the contract for handling @see GetImportJobState.
 */
interface GetImportJobStateHandlerInterface
{
    public function handle(GetImportJobState $query): ImportJobState;
}
