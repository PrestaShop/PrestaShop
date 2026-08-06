<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Hook\Query;

/**
 * Returns the list of hooks a given module can be hooked to.
 * Used to populate the hook selector in the "Hook a module" form.
 */
class GetPossibleHooksForModule
{
    private int $moduleId;

    public function __construct(int $moduleId)
    {
        $this->moduleId = $moduleId;
    }

    public function getModuleId(): int
    {
        return $this->moduleId;
    }
}
