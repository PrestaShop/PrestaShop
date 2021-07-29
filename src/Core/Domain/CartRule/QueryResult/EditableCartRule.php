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

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\QueryResult;

use DateTime;
use PrestaShop\PrestaShop\Core\Domain\CartRule\ValueObject\CartRuleId;

/**
 * Provides data for editing CatalogPriceRule
 */
class EditableCartRule
{
    /**
     * @var CartRuleId
     */
    private $cartRuleId;

    /**
     * @var EditableCartRuleInformation
     */
    private $information;

    /**
     * @var EditableCartRuleConditions
     */
    private $conditions;

    /**
     * @var EditableCartRuleActions
     */
    private $actions;

    /**
     * @var DateTime|null
     */
    private $dateAdd;

    /**
     * @var DateTime|null
     */
    private $dateUpd;

    public function __construct(
        CartRuleId $cartRuleId,
        EditableCartRuleInformation $information,
        EditableCartRuleConditions $conditions,
        EditableCartRuleActions $actions,
        ?DateTime $dateAdd,
        ?DateTime $dateUpd
    ) {
        $this->cartRuleId = $cartRuleId;
        $this->information = $information;
        $this->conditions = $conditions;
        $this->actions = $actions;
        $this->dateAdd = $dateAdd;
        $this->dateUpd = $dateUpd;
    }

    /**
     * @return CartRuleId
     */
    public function getCartRuleId(): CartRuleId
    {
        return $this->cartRuleId;
    }

    /**
     * @return EditableCartRuleInformation
     */
    public function getInformation(): EditableCartRuleInformation
    {
        return $this->information;
    }

    /**
     * @return EditableCartRuleConditions
     */
    public function getConditions(): EditableCartRuleConditions
    {
        return $this->conditions;
    }

    /**
     * @return EditableCartRuleActions
     */
    public function getActions(): EditableCartRuleActions
    {
        return $this->actions;
    }

    /**
     * @return DateTime|null
     */
    public function getDateAdd(): ?DateTime
    {
        return $this->dateAdd;
    }

    /**
     * @return DateTime|null
     */
    public function getDateUpd(): ?DateTime
    {
        return $this->dateUpd;
    }
}
