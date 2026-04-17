<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Filter;

use PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinitionInterface;
use Symfony\Component\Form\FormInterface;

/**
 * Interface FilterFormFactoryInterface.
 */
interface GridFilterFormFactoryInterface
{
    /**
     * Create filters form for grid definition.
     *
     * @param GridDefinitionInterface $definition
     *
     * @return FormInterface
     */
    public function create(GridDefinitionInterface $definition);
}
