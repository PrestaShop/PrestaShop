<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Store;

/**
 * Converts store opening hours between the localized "HH:MM | HH:MM" string form used by the
 * domain/forms and the per-language JSON form persisted in the legacy Store ObjectModel.
 */
final class HoursEncoder
{
    /**
     * @param array<int, array<int, string>> $localizedHours keyed by lang id, each value is an array of day strings
     *
     * @return array<int, string> keyed by lang id, each value is a JSON string
     */
    public function encode(array $localizedHours): array
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

    /**
     * @param array<int, string> $rawHours keyed by lang id, each value is a JSON string
     *
     * @return array<int, array<int, string>> keyed by lang id, each value is an array of 7 day strings
     */
    public function decode(array $rawHours): array
    {
        $result = [];
        foreach ($rawHours as $langId => $jsonString) {
            if (empty($jsonString)) {
                $result[(int) $langId] = array_fill(0, 7, '');
                continue;
            }
            $decoded = json_decode($jsonString, true);
            if (!is_array($decoded)) {
                $result[(int) $langId] = array_fill(0, 7, '');
                continue;
            }
            $days = [];
            foreach ($decoded as $day) {
                if (is_array($day) && 2 === count($day)) {
                    // New format: ["09:00", "18:00"] → "09:00 | 18:00"
                    $open = trim($day[0]);
                    $close = trim($day[1]);
                    $days[] = ($open !== '' && $close !== '') ? $open . ' | ' . $close : $open;
                } elseif (is_array($day) && count($day) > 0) {
                    // Legacy format: ["09:00AM - 07:00PM"] → use as-is, and legacy days can
                    // also carry more than two slots (["09:00","12:00","14:00","18:00"]):
                    // join whatever is there rather than assuming at most 2.
                    $days[] = implode(' | ', array_map('trim', $day));
                } else {
                    $days[] = '';
                }
            }
            $result[(int) $langId] = $days;
        }

        return $result;
    }
}
