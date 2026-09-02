<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Address\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Address\Query\GetRequiredFieldsForAddress;

interface GetRequiredFieldsForAddressHandlerInterface
{
    /**
     * @param GetRequiredFieldsForAddress $query
     *
     * @return string[]
     */
    public function handle(GetRequiredFieldsForAddress $query): array;
}
