<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\B2bRole;

final class Permission
{
    public const BUSINESS_ENTITY_EDIT = 'B2B_BUSINESS_ENTITY_EDIT';

    public const BUSINESS_ENTITY_CUSTOMER_INVITE = 'B2B_BUSINESS_ENTITY_CUSTOMER_INVITE';
    public const BUSINESS_ENTITY_CUSTOMER_EDIT = 'B2B_BUSINESS_ENTITY_CUSTOMER_EDIT';
    public const BUSINESS_ENTITY_CUSTOMER_DELETE = 'B2B_BUSINESS_ENTITY_CUSTOMER_DELETE';

    public const ORDER_VIEW = 'B2B_ORDER_VIEW';
    public const ORDER_CREATE = 'B2B_ORDER_CREATE';

    private function __construct()
    {
    }
}
