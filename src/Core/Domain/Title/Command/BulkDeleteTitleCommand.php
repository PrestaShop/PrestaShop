<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Title\Command;

use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\TitleId;

/**
 * Deletes states on bulk action
 */
class BulkDeleteTitleCommand
{
    /**
     * @var array<int, TitleId>
     */
    private $titleIds;

    /**
     * @param array<int, int> $titleIds
     */
    public function __construct(array $titleIds)
    {
        $this->setTitleIds($titleIds);
    }

    /**
     * @return array<int, TitleId>
     */
    public function getTitleIds(): array
    {
        return $this->titleIds;
    }

    /**
     * @param array<int, int> $titleIds
     */
    private function setTitleIds(array $titleIds): void
    {
        foreach ($titleIds as $stateId) {
            $this->titleIds[] = new TitleId((int) $stateId);
        }
    }
}
