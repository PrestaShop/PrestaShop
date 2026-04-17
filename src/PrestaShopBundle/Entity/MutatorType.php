<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Entity;

enum MutatorType: string
{
    case EMPLOYEE = 'employee';
    case API_CLIENT = 'api_client';
    case MODULE = 'module';
}
