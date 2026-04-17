<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Grid\Presenter;

use PrestaShop\PrestaShop\Core\Grid\GridInterface;

/**
 * Interface GridPresenterInterface defines contract for grid presenter.
 */
interface GridPresenterInterface
{
    /**
     * Present grid as plain array.
     *
     * @param GridInterface $grid
     *
     * @return array
     */
    public function present(GridInterface $grid);
}
