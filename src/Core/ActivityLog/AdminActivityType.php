<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ActivityLog;

enum AdminActivityType: string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
    case DUPLICATE = 'duplicate';
    case ACTIVATE = 'activate';
    case DEACTIVATE = 'deactivate';
}
