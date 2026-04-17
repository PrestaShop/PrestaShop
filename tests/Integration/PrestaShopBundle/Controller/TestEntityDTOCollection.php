<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller;

use PrestaShop\PrestaShop\Core\Data\AbstractTypedCollection;

class TestEntityDTOCollection extends AbstractTypedCollection
{
    /**
     * @var int
     */
    private $totalCount = 0;

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     */
    public function setTotalCount(int $totalCount): void
    {
        $this->totalCount = $totalCount;
    }

    protected function getType()
    {
        return TestEntityDTO::class;
    }
}
