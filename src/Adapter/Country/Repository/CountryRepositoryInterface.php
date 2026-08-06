<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Country\Repository;

use Country;
use PrestaShop\PrestaShop\Core\Domain\Country\ValueObject\CountryId;

interface CountryRepositoryInterface
{
    public function assertCountryExists(CountryId $countryId): void;

    public function get(CountryId $countryId): Country;

    public function add(Country $country): Country;

    public function update(Country $country): Country;

    public function delete(CountryId $countryId): void;
}
