<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\SqlManager\QueryHandler;

use PrestaShop\PrestaShop\Adapter\Entity\RequestSql;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\EditableRequestSql;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\RequestSqlException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\RequestSqlNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetRequestSqlForEditingQuery;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\QueryHandler\GetRequestSqlForEditingHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\RequestSqlId;

final class GetRequestSqlForEditingHandler implements GetRequestSqlForEditingHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws RequestSqlException
     * @throws RequestSqlNotFoundException
     */
    public function handle(GetRequestSqlForEditingQuery $query)
    {
        $entity = $this->loadById($query->getRequestSqlId());

        return $this->buildEditableRequestSql($entity);
    }

    /**
     * @param RequestSqlId $requestSqlId
     *
     * @return RequestSql
     *
     * @throws RequestSqlNotFoundException
     */
    private function loadById(RequestSqlId $requestSqlId)
    {
        $entity = new RequestSql($requestSqlId->getValue());

        if (0 >= $entity->id) {
            throw new RequestSqlNotFoundException(
                sprintf('RequestSql with id "%s" cannot be found', $requestSqlId->getValue())
            );
        }

        if ((int) $entity->id !== $requestSqlId->getValue()) {
            throw new RequestSqlNotFoundException(
                sprintf(
                    'The retrieved id "%s" does not match requested RequestSql id "%s"',
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
     * @return EditableRequestSql
     *
     * @throws RequestSqlException
     */
    private function buildEditableRequestSql(RequestSql $entity)
    {
        return new EditableRequestSql(
            new RequestSqlId($entity->id),
            $entity->name,
            $entity->sql
        );
    }
}
