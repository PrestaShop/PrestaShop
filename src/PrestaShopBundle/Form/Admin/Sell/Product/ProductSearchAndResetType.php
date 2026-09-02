<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Product;

use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Special search and reset button for product grid, the reset button is not shown when
 * category is the only filter selected.
 */
class ProductSearchAndResetType extends SearchAndResetType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        if (null !== $form->getParent()) {
            $selectedFilters = array_keys($form->getParent()->getData());

            if (count($selectedFilters) === 1 && $selectedFilters[0] === 'id_category') {
                $view->vars['show_reset_button'] = false;
            }
        }
    }
}
