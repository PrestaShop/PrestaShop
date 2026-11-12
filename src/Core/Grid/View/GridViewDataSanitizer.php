<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\View;

/**
 * Sanitizes the client-provided parts of a grid view (dynamic date rules and grid state)
 * against the search criteria persisted server-side.
 */
class GridViewDataSanitizer
{
    /**
     * @param array $dateRules
     * @param array $searchCriteria
     *
     * @return array|null
     */
    public function sanitizeDateRules(array $dateRules, array $searchCriteria): ?array
    {
        $sanitizedRules = [];

        foreach ($dateRules as $field => $ruleConfig) {
            $filterValue = $searchCriteria['filters'][$field] ?? null;
            if (!is_array($filterValue) || (!isset($filterValue['from']) && !isset($filterValue['to']))) {
                continue;
            }

            $rule = DynamicDateRule::tryFrom((string) ($ruleConfig['date_rule'] ?? ''));
            if (null === $rule || DynamicDateRule::KEEP_AS_IS === $rule) {
                continue;
            }

            $customDays = isset($ruleConfig['custom_days']) && is_numeric($ruleConfig['custom_days'])
                ? (int) $ruleConfig['custom_days']
                : null;

            if (DynamicDateRule::LAST_DAYS === $rule && (null === $customDays || $customDays < 1)) {
                continue;
            }

            $sanitizedRules[$field] = [
                'date_rule' => $rule->value,
                'custom_days' => DynamicDateRule::LAST_DAYS === $rule ? $customDays : null,
            ];
        }

        return [] !== $sanitizedRules ? $sanitizedRules : null;
    }

    /**
     * @param string|null $gridStateJson
     *
     * @return array|null
     */
    public function sanitizeGridState(?string $gridStateJson): ?array
    {
        if (empty($gridStateJson)) {
            return null;
        }

        $decodedState = json_decode($gridStateJson, true);
        if (!is_array($decodedState)) {
            return null;
        }

        return GridState::fromArray($decodedState)->toArray();
    }
}
