<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Employee\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\Employee\Query\GetEmployeeEmailById;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;

interface GetEmployeeEmailByIdHandlerInterface
{
    /**
     * @param GetEmployeeEmailById $query
     *
     * @return Email
     */
    public function handle(GetEmployeeEmailById $query): Email;
}
