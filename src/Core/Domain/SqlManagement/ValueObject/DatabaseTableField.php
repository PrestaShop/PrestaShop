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

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Exception\SqlManagementConstraintException;

/**
 * Class DatabaseTableField stores information about single database table field.
 */
class DatabaseTableField
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @param string $name
     * @param string $type
     *
     * @throws SqlManagementConstraintException
     */
    public function __construct($name, $type)
    {
        $this
            ->setName($name)
            ->setType($type);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return self
     *
     * @throws SqlManagementConstraintException
     */
    private function setName($name)
    {
        if (!is_string($name) || empty($name)) {
            throw new SqlManagementConstraintException(sprintf('Invalid database table field name %s supplied', var_export($name, true)), SqlManagementConstraintException::INVALID_DATABASE_TABLE_FIELD_NAME);
        }

        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return self
     *
     * @throws SqlManagementConstraintException
     */
    private function setType($type)
    {
        if (!is_string($type) || empty($type)) {
            throw new SqlManagementConstraintException(sprintf('Invalid database table field type %s supplied', var_export($type, true)), SqlManagementConstraintException::INVALID_DATABASE_TABLE_FIELD_TYPE);
        }

        $this->type = $type;

        return $this;
    }
}
