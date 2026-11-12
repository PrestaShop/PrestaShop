<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\CustomerB2b\Validate;

use PrestaShopBundle\Entity\B2B\B2bRole;
use PrestaShopBundle\Entity\B2B\BusinessEntity;

/**
 * @experimental
 */
final class CustomerB2bValidationResult
{
    public function __construct(
        public readonly BusinessEntity $businessEntity,
        public readonly B2bRole $role,
    ) {
    }
}
