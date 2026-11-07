<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\ActivityLog;

/**
 * Describes a successful Back Office activity that must be recorded in the activity log.
 */
final class AdminActivity
{
    public function __construct(
        private readonly AdminActivityType $type,
        private readonly string $objectType,
        private readonly ?int $objectId,
        private readonly ?int $logObjectId,
        private readonly ?int $newObjectId = null,
        private readonly bool $bulk = false,
    ) {
    }

    public function getType(): AdminActivityType
    {
        return $this->type;
    }

    public function getObjectType(): string
    {
        return $this->objectType;
    }

    public function getObjectId(): ?int
    {
        return $this->objectId;
    }

    public function getLogObjectId(): ?int
    {
        return $this->logObjectId;
    }

    public function getNewObjectId(): ?int
    {
        return $this->newObjectId;
    }

    public function isBulk(): bool
    {
        return $this->bulk;
    }
}
