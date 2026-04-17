<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Module\Query;

use PrestaShop\PrestaShop\Core\Domain\Module\ValueObject\ModuleTechnicalName;

/**
 * Get module information
 */
class GetModuleInfos
{
    protected ModuleTechnicalName $technicalName;

    public function __construct(
        string $technicalName,
    ) {
        $this->technicalName = new ModuleTechnicalName($technicalName);
    }

    public function getTechnicalName(): ModuleTechnicalName
    {
        return $this->technicalName;
    }
}
