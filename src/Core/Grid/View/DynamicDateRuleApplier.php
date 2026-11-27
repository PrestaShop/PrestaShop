<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Grid\View;

class DynamicDateRuleApplier
{
    /**
     * @param DynamicDateRangeComputer $dateRangeComputer
     */
    public function __construct(
        private readonly DynamicDateRangeComputer $dateRangeComputer,
    ) {
    }

    /**
     * @param array<string, mixed> $searchCriteria search criteria in the persisted format
     * @param array<string, array{date_rule?: string, custom_days?: int|null}> $dateRules indexed by filter field name
     *
     * @return array<string, mixed>
     */
    public function apply(array $searchCriteria, array $dateRules): array
    {
        foreach ($dateRules as $field => $ruleConfig) {
            $rule = DynamicDateRule::tryFrom($ruleConfig['date_rule'] ?? '');

            if (null === $rule || DynamicDateRule::KEEP_AS_IS === $rule) {
                continue;
            }

            $customDays = isset($ruleConfig['custom_days']) ? (int) $ruleConfig['custom_days'] : null;
            $range = $this->dateRangeComputer->compute($rule, $customDays);

            if (null !== $range && isset($searchCriteria['filters'][$field])) {
                $searchCriteria['filters'][$field] = $range;
            }
        }

        return $searchCriteria;
    }
}
