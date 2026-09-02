<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Import\EntityField\Provider\EntityFieldsProviderFinderInterface;
use PrestaShop\PrestaShop\Core\Import\Exception\NotSupportedImportEntityException;

/**
 * Class ImportEntityFieldChoiceProvider is responsible for providing entity import field choices.
 */
final class ImportEntityFieldChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var EntityFieldsProviderFinderInterface
     */
    private $entityFieldsProviderFinder;

    /**
     * @var int selected import entity
     */
    private $selectedEntity;

    /**
     * @param EntityFieldsProviderFinderInterface $entityFieldsProviderFinder
     * @param int $selectedEntity
     */
    public function __construct(
        EntityFieldsProviderFinderInterface $entityFieldsProviderFinder,
        $selectedEntity
    ) {
        $this->entityFieldsProviderFinder = $entityFieldsProviderFinder;
        $this->selectedEntity = $selectedEntity;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        try {
            $entityFieldCollection = $this->entityFieldsProviderFinder->find($this->selectedEntity)->getCollection();
        } catch (NotSupportedImportEntityException) {
            return [];
        }

        $choices = [];

        foreach ($entityFieldCollection as $entityField) {
            $label = $entityField->getLabel();

            if ($entityField->isRequired()) {
                $label .= '*';
            }

            $choices[$label] = $entityField->getName();
        }

        return $choices;
    }
}
