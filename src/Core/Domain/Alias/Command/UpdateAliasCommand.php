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

namespace PrestaShop\PrestaShop\Core\Domain\Alias\Command;

use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\AliasId;

/**
 * Edits alias with given data
 */
class UpdateAliasCommand
{
    public readonly AliasId $aliasId;

    /**
     * @var string[]
     */
    private $aliases;

    /**
     * @var string
     */
    private $searchTerm;

    /**
     * @var bool
     */
    private $active;

    /**
     * @param int $aliasId
     */
    public function __construct(int $aliasId)
    {
        $this->aliasId = new AliasId($aliasId);
    }

    /**
     * @return string[]
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @param string[] $aliases
     *
     * @return UpdateAliasCommand
     */
    public function setAliases(array $aliases): UpdateAliasCommand
    {
        $this->aliases = $aliases;

        return $this;
    }

    /**
     * @return string
     */
    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }

    /**
     * @param string $searchTerm
     *
     * @return UpdateAliasCommand
     */
    public function setSearchTerm(string $searchTerm): UpdateAliasCommand
    {
        $this->searchTerm = $searchTerm;

        return $this;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return UpdateAliasCommand
     */
    public function setActive(bool $active): UpdateAliasCommand
    {
        $this->active = $active;

        return $this;
    }
}
