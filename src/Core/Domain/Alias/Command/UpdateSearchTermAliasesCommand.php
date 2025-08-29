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
use PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject\SearchTerm;

/**
 * Updates search term aliases
 */
class UpdateSearchTermAliasesCommand
{
    private SearchTerm $oldSearchTerm;
    private SearchTerm $newSearchTerm;

    /**
     * @param string $oldSearchTerm
     * @param string|null $newSearchTerm
     * @param array{
     *   array{
     *     alias: string,
     *     active: bool,
     *   }
     * } $aliases
     */
    public function __construct(
        string $oldSearchTerm,
        private array $aliases,
        ?string $newSearchTerm = null,
    ) {
        $this->assertAliasesNotEmpty($aliases);
        $this->assertStringNotEmpty($oldSearchTerm);

        $this->oldSearchTerm = new SearchTerm($oldSearchTerm);
        $this->newSearchTerm = new SearchTerm((null === $newSearchTerm) ? $oldSearchTerm : $newSearchTerm);
    }

    public function getOldSearchTerm(): SearchTerm
    {
        return $this->oldSearchTerm;
    }

    public function getNewSearchTerm(): SearchTerm
    {
        return $this->newSearchTerm;
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
     * @param array<int, array<string, bool|string>> $aliases
     *
     * @throws AliasConstraintException
     */
    private function assertAliasesNotEmpty(array $aliases): void
    {
        if (!empty($aliases)) {
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
     * @throws AliasConstraintException
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
