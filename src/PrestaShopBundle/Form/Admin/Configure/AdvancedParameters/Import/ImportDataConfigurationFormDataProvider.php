<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Import;

use PrestaShop\PrestaShop\Adapter\Import\DataMatchSaver;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\Factory\DataRowCollectionFactoryInterface;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use PrestaShopBundle\Entity\Repository\ImportMatchRepository;
use SplFileInfo;

/**
 * Class ImportDataConfigurationFormDataProvider is responsible for providing Import's 2nd step form data.
 */
final class ImportDataConfigurationFormDataProvider implements ImportFormDataProviderInterface
{
    /**
     * @var ImportDirectory
     */
    private $importDirectory;

    /**
     * @var DataRowCollectionFactoryInterface
     */
    private $dataRowCollectionFactory;

    /**
     * @var array
     */
    private $entityFieldChoices;

    /**
     * @var ImportMatchRepository
     */
    private $importMatchRepository;

    /**
     * @var DataMatchSaver
     */
    private $dataMatchSaver;

    /**
     * @param ImportDirectory $importDirectory
     * @param DataRowCollectionFactoryInterface $dataRowCollectionFactory
     * @param ImportMatchRepository $importMatchRepository
     * @param DataMatchSaver $dataMatchSaver
     * @param array $entityFieldChoices
     */
    public function __construct(
        ImportDirectory $importDirectory,
        DataRowCollectionFactoryInterface $dataRowCollectionFactory,
        ImportMatchRepository $importMatchRepository,
        DataMatchSaver $dataMatchSaver,
        array $entityFieldChoices
    ) {
        $this->importDirectory = $importDirectory;
        $this->dataRowCollectionFactory = $dataRowCollectionFactory;
        $this->entityFieldChoices = $entityFieldChoices;
        $this->importMatchRepository = $importMatchRepository;
        $this->dataMatchSaver = $dataMatchSaver;
    }

    /**
     * {@inheritdoc}
     */
    public function getData(ImportConfigInterface $importConfig)
    {
        $importFile = new SplFileInfo($this->importDirectory . $importConfig->getFileName());
        $dataRowCollection = $this->dataRowCollectionFactory->buildFromFile($importFile, 1);

        // Getting the number of cells in the first row
        $rowSize = count($dataRowCollection->offsetGet(0));

        $data = [
            'csv' => $importConfig->getFileName(),
            'entity' => $importConfig->getEntityType(),
            'iso_lang' => $importConfig->getLanguageIso(),
            'separator' => $importConfig->getSeparator(),
            'multiple_value_separator' => $importConfig->getMultipleValueSeparator(),
            'truncate' => $importConfig->truncate(),
            'regenerate' => $importConfig->skipThumbnailRegeneration(),
            'match_ref' => $importConfig->matchReferences(),
            'forceIDs' => $importConfig->forceIds(),
            'sendemail' => $importConfig->sendEmail(),
            'type_value' => [],
        ];

        $numberOfValuesAdded = 0;

        // Add as many values to the configuration as the are cells in the row
        foreach ($this->entityFieldChoices as $choice) {
            // If we already added the required number of values - stop adding them
            if ($numberOfValuesAdded >= $rowSize) {
                break;
            }

            $data['type_value'][] = $choice;
            ++$numberOfValuesAdded;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        $errors = [];

        if (empty($data['match_name'])) {
            $errors[] = [
                'key' => 'Please name your data matching configuration in order to save it.',
                'domain' => 'Admin.Advparameters.Feature',
                'parameters' => [],
            ];
        }

        if ($this->configurationNameExists($data['match_name'])) {
            $errors[] = [
                'key' => 'This name already exists.',
                'domain' => 'Admin.Design.Notification',
                'parameters' => [],
            ];
        }

        if (empty($errors)) {
            $this->dataMatchSaver->save(
                $data['match_name'],
                $data['type_value'],
                $data['skip']
            );
        }

        return $errors;
    }

    /**
     * Checks if the configuration is already saved with the same name.
     *
     * @param string $matchName
     *
     * @return bool
     */
    private function configurationNameExists($matchName)
    {
        return (bool) $this->importMatchRepository->findOneByName($matchName);
    }
}
