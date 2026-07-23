<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Stats;

use Module;
use ModuleGraph;
use PrestaShop\PrestaShop\Core\Stats\Exception\StatsModuleNotFoundException;

/**
 * Wraps the legacy ModuleGraph rendering engine used by "stats*" modules to draw their charts.
 */
final class StatsModuleGraphRenderer
{
    /**
     * @throws StatsModuleNotFoundException
     */
    public function draw(
        string $module,
        string $render,
        string $type,
        int $width,
        int $height,
        $layers,
        ?string $option,
        int $employeeId,
        int $langId
    ): string {
        /** @var ModuleGraph|false $graph */
        $graph = Module::getInstanceByName($module);
        if (false === $graph) {
            throw new StatsModuleNotFoundException(sprintf('Graph module "%s" could not be loaded.', $module));
        }

        $graph->setEmployee($employeeId);
        $graph->setLang($langId);
        if ($option) {
            $graph->setOption($option, $layers);
        }

        $graph->create($render, $type, $width, $height, $layers);

        ob_start();
        $graph->draw();

        return (string) ob_get_clean();
    }
}
