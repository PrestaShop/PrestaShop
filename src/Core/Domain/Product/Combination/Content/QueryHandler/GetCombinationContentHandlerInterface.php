<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\Combination\Content\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Content\Query\GetCombinationContent;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Content\QueryResult\CombinationContent;

interface GetCombinationContentHandlerInterface
{
    public function handle(GetCombinationContent $query): CombinationContent;
}
