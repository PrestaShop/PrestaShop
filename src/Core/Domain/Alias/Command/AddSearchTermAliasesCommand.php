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

use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasConstraintException;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;

/**
 * Adds new search term with given aliases
 */
class AddSearchTermAliasesCommand
{
    /**
     * @var string
     */
    private $searchTerm;

    /**
     * @var array{
     *   array{
     *     alias: string,
     *     active: bool,
     *   }
     * }
     */
    private $aliases;

    /**
     * @param string $searchTerm
     * @param array $aliases
     */
    public function __construct(array $aliases, string $searchTerm)
    {
        $this->assertArrayNotEmpty($aliases);
        $this->assertStringNotEmpty($searchTerm);

        $this->aliases = $aliases;
        $this->searchTerm = $searchTerm;
    }

    /**
     * @return array{
     *   array{
     *     alias: string,
     *     active: bool,
     *   }
     * }
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

    /**
     * @param string[] $array
     *
     * @throws AliasConstraintException
     */
    private function assertArrayNotEmpty(array $array): void
    {
        if (!empty($array)) {
            return;
        }

        throw new AliasConstraintException(
            'Alias parameter aliases must not be empty',
            AliasConstraintException::INVALID_ALIAS
        );
    }

    /**
     * @param string $string
     *
     * @throws InvalidArgumentException
     */
    private function assertStringNotEmpty(string $string): void
    {
        if (!empty($string)) {
            return;
        }

        throw new AliasConstraintException(
            'Alias parameter search term must not be empty',
            AliasConstraintException::INVALID_SEARCH
        );
    }
}
