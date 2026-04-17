<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Title\Command;

use PrestaShop\PrestaShop\Core\Domain\Title\ValueObject\TitleId;

/**
 * Deletes state
 */
class DeleteTitleCommand
{
    /**
     * @var TitleId
     */
    private $titleId;

    /**
     * @param int $titleId
     */
    public function __construct(int $titleId)
    {
        $this->titleId = new TitleId($titleId);
    }

    /**
     * @return TitleId
     */
    public function getTitleId(): TitleId
    {
        return $this->titleId;
    }
}
