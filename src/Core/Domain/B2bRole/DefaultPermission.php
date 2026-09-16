<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\B2bRole;

/**
 * The B2B permissions created on installation.
 */
enum DefaultPermission: string
{
    case BUSINESS_ENTITY_EDIT = 'B2B_BUSINESS_ENTITY_EDIT';
    case BUSINESS_ENTITY_CUSTOMER_INVITE = 'B2B_BUSINESS_ENTITY_CUSTOMER_INVITE';
    case BUSINESS_ENTITY_CUSTOMER_EDIT = 'B2B_BUSINESS_ENTITY_CUSTOMER_EDIT';
    case BUSINESS_ENTITY_CUSTOMER_DELETE = 'B2B_BUSINESS_ENTITY_CUSTOMER_DELETE';
    case ORDER_VIEW = 'B2B_ORDER_VIEW';
    case ORDER_CREATE = 'B2B_ORDER_CREATE';
}
