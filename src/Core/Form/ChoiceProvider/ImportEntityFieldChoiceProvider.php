<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldCollectionInterface;
use PrestaShop\PrestaShop\Core\Import\EntityField\EntityFieldInterface;
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
    public function __construct(EntityFieldsProviderFinderInterface $entityFieldsProviderFinder, $selectedEntity)
    {
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
        } catch (NotSupportedImportEntityException $e) {
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
