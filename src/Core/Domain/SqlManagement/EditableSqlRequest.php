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

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlRequestException;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject\SqlRequestId;

/**
 * Class EditableSqlRequest stores information about SqlRequest that can be edited.
 */
class EditableSqlRequest
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
     * @param SqlRequestId $requestSqlId
     * @param string $name
     * @param string $sql
     *
     * @throws SqlRequestException
     */
    public function __construct(
        SqlRequestId $requestSqlId,
        $name,
        $sql
    ) {
        $this
            ->setSqlRequestId($requestSqlId)
            ->setName($name)
            ->setSql($sql);
    }

    /**
     * @return SqlRequestId
     */
    public function getSqlRequestId()
    {
        return $this->sqlRequestId;
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
     * @param SqlRequestId $sqlRequestId
     *
     * @return EditableSqlRequest
     */
    private function setSqlRequestId(SqlRequestId $sqlRequestId)
    {
        $this->sqlRequestId = $sqlRequestId;

        return $this;
    }

    /**
     * @param string $name
     *
     * @return EditableSqlRequest
     *
     * @throws SqlRequestException
     */
    private function setName($name)
    {
        if (empty($name)) {
            throw new SqlRequestException('SqlRequest name cannot be empty');
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @param string $sql
     *
     * @return EditableSqlRequest
     *
     * @throws SqlRequestException
     */
    private function setSql($sql)
    {
        if (empty($sql)) {
            throw new SqlRequestException('SqlRequest SQL cannot be empty');
        }

        $this->sql = $sql;

        return $this;
    }
}
