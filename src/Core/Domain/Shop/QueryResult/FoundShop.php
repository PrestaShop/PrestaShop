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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Shop\QueryResult;

/**
 * Holds data of a shop found via a SearchShop query
 */
class FoundShop
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $color;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $groupId;

    /**
     * @var string
     */
    private $groupName;

    /**
     * @var string
     */
    private $groupColor;

    /**
     * FoundShop constructor.
     *
     * @param int $id
     * @param string $color
     * @param string $name
     * @param int $groupId
     * @param string $groupName
     */
    public function __construct(
        int $id,
        string $color,
        string $name,
        int $groupId,
        string $groupName,
        string $groupColor
    ) {
        $this->id = $id;
        $this->color = $color;
        $this->name = $name;
        $this->groupId = $groupId;
        $this->groupName = $groupName;
        $this->groupColor = $groupColor;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getGroupId(): int
    {
        return $this->groupId;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function getGroupColor(): string
    {
        return $this->groupColor;
    }
}
