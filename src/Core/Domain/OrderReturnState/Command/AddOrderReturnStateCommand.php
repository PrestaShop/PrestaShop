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
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Command;

use PrestaShop\PrestaShop\Core\Domain\OrderReturnState\Exception\OrderReturnStateConstraintException;

/**
 * Adds new order return state with provided data
 */
class AddOrderReturnStateCommand
{
    /**
     * @var string[]
     */
    private $localizedNames;
    /**
     * @var string
     */
    private $color;

    public function __construct(
        array $localizedNames,
        string $color
    ) {
        $this->setLocalizedNames($localizedNames);
        $this->color = $color;
    }

    /**
     * @return string[]
     */
    public function getLocalizedNames()
    {
        return $this->localizedNames;
    }

    /**
     * @param string[] $localizedNames
     *
     * @return $this
     *
     * @throws OrderReturnStateConstraintException
     */
    public function setLocalizedNames(array $localizedNames)
    {
        if (empty($localizedNames)) {
            throw new OrderReturnStateConstraintException('Order return status name cannot be empty', OrderReturnStateConstraintException::EMPTY_NAME);
        }

        $this->localizedNames = $localizedNames;

        return $this;
    }

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }
}
