<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Store\Trait;

use Country;
use PrestaShop\PrestaShop\Core\Domain\Store\Exception\StoreConstraintException;

trait StoreHandlerTrait
{
    private function assertStateCountryConsistency(int $countryId, ?int $stateId): void
    {
        $country = new Country($countryId);

        if ($country->contains_states && !$stateId) {
            throw new StoreConstraintException(
                'A state is required for the selected country.',
                StoreConstraintException::INVALID_STATE
            );
        }

        if (!$country->contains_states && $stateId) {
            throw new StoreConstraintException(
                'The selected country does not contain states.',
                StoreConstraintException::STATE_COUNTRY_MISMATCH
            );
        }
    }

    /**
     * @param array<int, array<int, string>> $localizedHours
     *
     * @return array<int, string>
     */
    private function encodeHours(array $localizedHours): array
    {
        $result = [];
        foreach ($localizedHours as $langId => $days) {
            $encoded = [];
            foreach ($days as $day) {
                $parts = array_map('trim', explode('|', (string) $day, 2));
                $encoded[] = isset($parts[1]) ? [$parts[0], $parts[1]] : [$parts[0]];
            }
            $result[(int) $langId] = json_encode($encoded);
        }

        return $result;
    }
}
