<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\State;

use State;

class CountryStateByIsoCodeProvider
{
    /**
     * @param string $isoCode
     * @param int|null $countryId
     *
     * @return int
     */
    public function getStateIdByIsoCode(string $isoCode, ?int $countryId = null): int
    {
        return (int) State::getIdByIso($isoCode, $countryId);
    }
}
