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

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;

/**
 * Class EditSqlRequestCommand defines command for SqlRequest editing
 */
class EditSqlRequestCommand
{
    /**
     * @var SqlRequestId
     */
    private $sqlRequestId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $sql;

    /**
     * @param SqlRequestId $sqlRequestId
     * @param string $name
     * @param string $sql
     */
    public function __construct(
        SqlRequestId $sqlRequestId,
        $name,
        $sql
    ) {
        $this
            ->setSqlRequestId($sqlRequestId)
            ->setName($name)
            ->setSql($sql)
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @return SqlRequestId
     */
    public function getSqlRequestId()
    {
        return $this->sqlRequestId;
    }

    /**
     * @param SqlRequestId $sqlRequestId
     *
     * @return EditSqlRequestCommand
     */
    private function setSqlRequestId(SqlRequestId $sqlRequestId)
    {
        $this->sqlRequestId = $sqlRequestId;

        return $this;
    }

    /**
     * Set Request SQL name
     *
     * @param string $name
     *
     * @return self
     *
     * @throws SqlRequestConstraintException
     */
    private function setName($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new SqlRequestConstraintException(
                sprintf('Invalid RequestSql name "%s"', var_export($name, true)),
                SqlRequestConstraintException::INVALID_NAME_ERROR
            );
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Set Request SQL query
     *
     * @param string $sql
     *
     * @return $this
     *
     * @throws SqlRequestConstraintException
     */
    private function setSql($sql)
    {
        if (!is_string($sql) || empty($sql)) {
            throw new SqlRequestConstraintException(
                sprintf('Invalid RequestSql SQL query "%s"', var_export($sql, true)),
                SqlRequestConstraintException::INVALID_SQL_QUERY_ERROR
            );
        }

        $this->sql = $sql;

        return $this;
    }
}
