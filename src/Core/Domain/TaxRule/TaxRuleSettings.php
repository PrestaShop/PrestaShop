<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TaxRule;

class TaxRuleSettings
{
    public const BEHAVIOR_TAX_ONLY = 0;
    public const BEHAVIOR_COMBINE = 1;
    public const BEHAVIOR_ONE_AFTER_ANOTHER = 2;
}
