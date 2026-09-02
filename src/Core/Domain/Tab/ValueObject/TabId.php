<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Tab\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Tab\Exception\InvalidTabValueIdException;

class TabId implements TabIdInterface
{
    /**
     * @var int
     */
    private $tabId;

    /**
     * @param int $tabId
     */
    public function __construct(int $tabId)
    {
        $this->assertTabIdIsGreaterThanZero($tabId);

        $this->tabId = $tabId;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->tabId;
    }

    /**
     * @param int $tabId
     *
     * @throws InvalidTabValueIdException
     */
    private function assertTabIdIsGreaterThanZero(int $tabId): void
    {
        if (0 >= $tabId) {
            throw new InvalidTabValueIdException(
                sprintf('Invalid tab id "%d" provided', $tabId)
            );
        }
    }
}
