<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Kpi;

/**
 * Interface RenderableKpi describes a renderable KPI.
 */
interface RenderableKpi
{
    /**
     * Renders the KPI's view.
     *
     * @return string
     */
    public function render();
}
