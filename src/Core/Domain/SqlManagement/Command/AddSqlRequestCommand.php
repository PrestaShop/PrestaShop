<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestConstraintException;

/**
 * This command creates new SqlRequest entity with provided data.
 */
class AddSqlRequestCommand
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $sql;

    /**
     * @param string $name
     * @param string $sql
     *
     * @throws SqlRequestConstraintException
     */
    public function __construct($name, $sql)
    {
        $this
            ->setName($name)
            ->setSql($sql);
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
     * Set Request SQL name.
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
            throw new SqlRequestConstraintException(sprintf('Invalid SqlRequest name %s', var_export($name, true)), SqlRequestConstraintException::INVALID_NAME);
        }

        $this->name = $name;

        return $this;
    }

    /**
     * Set Request SQL query.
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
            throw new SqlRequestConstraintException(sprintf('Invalid SqlRequest SQL query %s', var_export($sql, true)), SqlRequestConstraintException::INVALID_SQL_QUERY);
        }

        $this->sql = $sql;

        return $this;
    }
}
