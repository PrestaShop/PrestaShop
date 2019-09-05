<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\SqlManager\QueryHandler;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\EditableSqlRequest;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestForEditing;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\QueryHandler\GetSqlRequestForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;
use RequestSql;

/**
 * Class GetSqlRequestForEditingHandler.
 *
 * @internal
 */
final class GetSqlRequestForEditingHandler implements GetSqlRequestForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws SqlRequestException
     * @throws SqlRequestNotFoundException
     */
    public function handle(GetSqlRequestForEditing $query)
    {
        $entity = $this->loadById($query->getRequestSqlId());

        return $this->buildEditableSqlRequest($entity);
    }

    /**
     * @param SqlRequestId $requestSqlId
     *
     * @return RequestSql
     *
     * @throws SqlRequestNotFoundException
     */
    private function loadById(SqlRequestId $requestSqlId)
    {
        $entity = new RequestSql($requestSqlId->getValue());

        if (0 >= $entity->id) {
            throw new SqlRequestNotFoundException(
                sprintf('SqlRequest with id "%s" cannot be found', $requestSqlId->getValue())
            );
        }

        if ((int) $entity->id !== $requestSqlId->getValue()) {
            throw new SqlRequestNotFoundException(
                sprintf(
                    'The retrieved id "%s" does not match requested SqlRequest id "%s"',
                    $entity->id,
                    $requestSqlId->getValue()
                )
            );
        }

        return $entity;
    }

    /**
     * @param RequestSql $entity
     *
     * @return EditableSqlRequest
     *
     * @throws SqlRequestException
     */
    private function buildEditableSqlRequest(RequestSql $entity)
    {
        return new EditableSqlRequest(
            new SqlRequestId($entity->id),
            $entity->name,
            $entity->sql
        );
    }
}
