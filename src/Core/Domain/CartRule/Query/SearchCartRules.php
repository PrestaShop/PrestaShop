<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CartRule\Query;

/**
 * Searches for cart rules
 */
class SearchCartRules
{
    /**
     * @var string
     */
    private $searchPhrase;

    /**
     * @param string $searchPhrase
     */
    public function __construct(string $searchPhrase)
    {
        $this->searchPhrase = $searchPhrase;
    }

    /**
     * @return string
     */
    public function getSearchPhrase(): string
    {
        return $this->searchPhrase;
    }
}
