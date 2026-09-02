<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Country\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryForEditing;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\CountryForEditing;

/**
 * Defines contract for get country for editing handler
 */
interface GetCountryForEditingHandlerInterface
{
    public function handle(GetCountryForEditing $command): CountryForEditing;
}
