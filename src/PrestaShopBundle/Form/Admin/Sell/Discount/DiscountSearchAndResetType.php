<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Sell\Discount;

use PrestaShopBundle\Form\Admin\Type\SearchAndResetType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * Similar component as the SearchAndResetType, except it handles an exception The reset button
 * is not shown when period_filter is the only selected filter.
 */
class DiscountSearchAndResetType extends SearchAndResetType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        parent::buildView($view, $form, $options);
        if (null !== $form->getParent()) {
            $availableValueNames = array_keys($form->getParent()->getData());
            if ($availableValueNames === ['period_filter']) {
                $view->vars['show_reset_button'] = false;
            }
        }
    }
}
