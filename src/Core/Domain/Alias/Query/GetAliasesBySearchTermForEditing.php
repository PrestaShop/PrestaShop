<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Alias\Query;

class GetAliasesBySearchTermForEditing
{
    public function __construct(
        private readonly string $searchTerm
    ) {
    }

    public function getSearchTerm(): string
    {
        return $this->searchTerm;
    }
}
