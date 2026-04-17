<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\CartRule;

/**
 * Class contains various constants representing cart rule constraints
 *
 * @deprecated To be removed in 10.0
 */
class CartRuleSettings
{
    public const NAME_MAX_LENGTH = 254;
    public const DESCRIPTION_MAX_LENGTH = 65534;
    public const CODE_MAX_LENGTH = 254;

    /**
     * This class is not designed for initialization
     */
    private function __construct()
    {
    }
}
