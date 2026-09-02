<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Alias\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Alias\Exception\AliasConstraintException;

/**
 * Defines alias search term with it's constraints.
 */
class SearchTerm
{
    public function __construct(
        private string $searchTerm
    ) {
        $this->assertStringNotEmpty($searchTerm);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->searchTerm;
    }

    private function assertStringNotEmpty(string $searchTerm): void
    {
        if (empty($searchTerm)) {
            throw new AliasConstraintException(
                'Search term cannot be empty.',
                AliasConstraintException::INVALID_SEARCH
            );
        }
    }
}
