<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
