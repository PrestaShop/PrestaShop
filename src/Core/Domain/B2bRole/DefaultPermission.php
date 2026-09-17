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
    public const PREFIX = 'ROLE_B2B_';

    case BUSINESS_ENTITY_EDIT = self::PREFIX . 'BUSINESS_ENTITY_EDIT';
    case BUSINESS_ENTITY_CUSTOMER_INVITE = self::PREFIX . 'BUSINESS_ENTITY_CUSTOMER_INVITE';
    case BUSINESS_ENTITY_CUSTOMER_EDIT = self::PREFIX . 'BUSINESS_ENTITY_CUSTOMER_EDIT';
    case BUSINESS_ENTITY_CUSTOMER_DELETE = self::PREFIX . 'BUSINESS_ENTITY_CUSTOMER_DELETE';
    case ORDER_VIEW = self::PREFIX . 'ORDER_VIEW';
    case ORDER_CREATE = self::PREFIX . 'ORDER_CREATE';
}
