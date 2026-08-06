<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\QuickAccess\Command;

/**
 * Creates a quick access link.
 *
 * Handler must enforce uniqueness by link URL since there is no DB UNIQUE KEY on the column.
 */
class AddQuickAccessCommand
{
    /** @var array<int, string> */
    private array $localizedNames;

    private string $link;

    private bool $newWindow;

    /**
     * @param array<int, string> $localizedNames Lang-ID-keyed name translations
     */
    public function __construct(array $localizedNames, string $link, bool $newWindow)
    {
        $this->localizedNames = $localizedNames;
        $this->link = $link;
        $this->newWindow = $newWindow;
    }

    /** @return array<int, string> */
    public function getLocalizedNames(): array
    {
        return $this->localizedNames;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function isNewWindow(): bool
    {
        return $this->newWindow;
    }
}
