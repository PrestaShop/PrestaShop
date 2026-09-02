<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Module\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Module\Exception\ModuleConstraintException;

class ModuleTechnicalName
{
    public function __construct(
        protected string $value,
    ) {
        $this->assertNotEmpty($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    private function assertNotEmpty(string $value): void
    {
        if (empty($value)) {
            throw new ModuleConstraintException('Technical name cannot be empty', ModuleConstraintException::EMPTY_TECHNICAL_NAME);
        }
    }
}
