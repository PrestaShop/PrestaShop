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
    private AliasId $aliasId;

    /**
     * @var string[]
     */
    private array $aliases;

    private string $searchTerm;

    /**
     * @param int $aliasId
     * @param string[] $aliases this input is array of string aliases that are returned from the form input
     * @param string $searchTerm
     */
    public function __construct(
        int $aliasId,
        array $aliases,
        string $searchTerm,
    ) {
        $this->aliasId = new AliasId($aliasId);
        $this->aliases = $aliases;
        $this->searchTerm = $searchTerm;
    }

    /**
     * @return AliasId
     */
    public function getAliasId(): AliasId
    {
        return $this->aliasId;
    }

    /**
     * Returns array of string aliases that are used in the alias form input.
     *
     * @return string[]
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * @return string
     */
    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }
}
