<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Attachment\Query;

use PrestaShop\PrestaShop\Core\Domain\Attachment\Exception\EmptySearchInputException;

class SearchAttachment
{
    /**
     * @var string
     */
    private $searchPhrase;

    public function __construct(
        string $searchPhrase
    ) {
        if (empty($searchPhrase)) {
            throw new EmptySearchInputException('Search parameter cannot be empty');
        }
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
