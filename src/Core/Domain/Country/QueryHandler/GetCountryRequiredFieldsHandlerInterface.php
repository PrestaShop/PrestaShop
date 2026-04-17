<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Country\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Country\Query\GetCountryRequiredFields;
use PrestaShop\PrestaShop\Core\Domain\Country\QueryResult\CountryRequiredFields;

/**
 * Defines contract for country required fields handler
 */
interface GetCountryRequiredFieldsHandlerInterface
{
    /**
     * @param GetCountryRequiredFields $query
     *
     * @return CountryRequiredFields
     */
    public function handle(GetCountryRequiredFields $query): CountryRequiredFields;
}
