<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceFormatter;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Supplier;

/**
 * Returns the list of selectable suppliers, including those which are disabled.
 */
final class SupplierNameByIdChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function getChoices()
    {
        return FormChoiceFormatter::formatFormChoices(
            Supplier::getSuppliers(false, 0, false),
            'id_supplier',
            'name'
        );
    }
}
