<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command;

use PrestaShop\PrestaShop\Core\Domain\QuickAccess\ValueObject\QuickAccessId;

/**
 * Partial-update command: only fields explicitly set via setters are persisted.
 * A null getter value means "not changed in this request", not "set to null in DB".
 */
class EditQuickAccessCommand
{
    private QuickAccessId $quickAccessId;

    /** @var array<int, string>|null */
    private ?array $localizedNames = null;

    private ?string $link = null;

    private ?bool $newWindow = null;

    public function __construct(int $quickAccessId)
    {
        $this->quickAccessId = new QuickAccessId($quickAccessId);
    }

    public function getQuickAccessId(): QuickAccessId
    {
        return $this->quickAccessId;
    }

    /** @return array<int, string>|null */
    public function getLocalizedNames(): ?array
    {
        return $this->localizedNames;
    }

    /** @param array<int, string> $localizedNames */
    public function setLocalizedNames(array $localizedNames): self
    {
        $this->localizedNames = $localizedNames;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getNewWindow(): ?bool
    {
        return $this->newWindow;
    }

    public function setNewWindow(bool $newWindow): self
    {
        $this->newWindow = $newWindow;

        return $this;
    }
}
