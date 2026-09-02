<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Module\Command;

/**
 * Bulk toggles module status
 */
class BulkToggleModuleStatusCommand
{
    /**
     * @var array<string>
     */
    private $modules;

    /**
     * @var bool
     */
    private $expectedStatus;

    /**
     * @param array<string> $modules Array of technical names for modules
     * @param bool $expectedStatus
     */
    public function __construct(array $modules, bool $expectedStatus)
    {
        $this->modules = $modules;
        $this->expectedStatus = $expectedStatus;
    }

    /**
     * @return array<string>
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * @return bool
     */
    public function getExpectedStatus(): bool
    {
        return $this->expectedStatus;
    }
}
