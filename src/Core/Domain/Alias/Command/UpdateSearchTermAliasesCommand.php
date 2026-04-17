<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
