<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\File\FileFinderInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * Class InvoiceModelByNameChoiceProvider provides invoice model choices with name values.
 */
final class InvoiceModelByNameChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var FileFinderInterface
     */
    private $invoiceModelFinder;

    /**
     * @param FileFinderInterface $invoiceModelFinder
     */
    public function __construct(FileFinderInterface $invoiceModelFinder)
    {
        $this->invoiceModelFinder = $invoiceModelFinder;
    }

    /**
     * Get invoice model choices.
     *
     * @return array
     */
    public function getChoices()
    {
        $choices = [
            'invoice' => 'invoice',
        ];

        $invoiceModels = $this->invoiceModelFinder->find();

        foreach ($invoiceModels as $invoiceModel) {
            $modelName = basename($invoiceModel, '.tpl');
            $choices[$modelName] = $modelName;
        }

        return $choices;
    }
}
