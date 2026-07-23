<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Stats;

use Module;
use ModuleGrid;
use PrestaShop\PrestaShop\Core\Stats\Exception\StatsModuleNotFoundException;

/**
 * Wraps the legacy ModuleGrid rendering engine used by "stats*" modules to draw their data grids.
 */
final class StatsModuleGridRenderer
{
    /**
     * @throws StatsModuleNotFoundException
     */
    public function render(
        string $module,
        string $render,
        string $type,
        int $width,
        int $height,
        int $start,
        int $limit,
        $sort,
        $dir,
        ?string $option,
        int $employeeId,
        int $langId
    ): string {
        /** @var ModuleGrid|false $grid */
        $grid = Module::getInstanceByName($module);
        if (false === $grid) {
            throw new StatsModuleNotFoundException(sprintf('Grid module "%s" could not be loaded.', $module));
        }

        $grid->setEmployee($employeeId);
        $grid->setLang($langId);
        if ($option) {
            $grid->setOption($option);
        }

        $grid->create($render, $type, $width, $height, $start, $limit, $sort, $dir);

        ob_start();
        $grid->render();

        return (string) ob_get_clean();
    }
}
