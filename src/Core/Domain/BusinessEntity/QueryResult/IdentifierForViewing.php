<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\BusinessEntity\QueryResult;

class IdentifierForViewing
{
    public function __construct(
        private readonly int $businessIdentifierId,
        private readonly string $label,
        private readonly ?string $value,
    ) {
    }

    public function getBusinessIdentifierId(): int
    {
        return $this->businessIdentifierId;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }
}
