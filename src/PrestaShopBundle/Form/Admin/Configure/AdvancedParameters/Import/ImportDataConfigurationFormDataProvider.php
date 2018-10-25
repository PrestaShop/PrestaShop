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

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Import;

use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;
use PrestaShop\PrestaShop\Core\Import\Configuration\ImportConfigInterface;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\Factory\DataRowCollectionFactoryInterface;
use PrestaShop\PrestaShop\Core\Import\File\FileFinder;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use SplFileInfo;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class ImportDataConfigurationFormDataProvider is responsible for providing Import's 2nd step form data.
 */
final class ImportDataConfigurationFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var FileFinder
     */
    private $importFileFinder;

    /**
     * @var ImportConfigInterface
     */
    private $importConfig;

    /**
     * @var SessionInterface
     */
    private $session;

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
     * @param FileFinder $importFileFinder
     * @param ImportConfigInterface $importConfig
     * @param SessionInterface $session
     * @param ImportDirectory $importDirectory
     * @param DataRowCollectionFactoryInterface $dataRowCollectionFactory
     * @param array $entityFieldChoices
     */
    public function __construct(
        FileFinder $importFileFinder,
        ImportConfigInterface $importConfig,
        SessionInterface $session,
        ImportDirectory $importDirectory,
        DataRowCollectionFactoryInterface $dataRowCollectionFactory,
        array $entityFieldChoices
    ) {
        $this->importFileFinder = $importFileFinder;
        $this->importConfig = $importConfig;
        $this->session = $session;
        $this->importDirectory = $importDirectory;
        $this->dataRowCollectionFactory = $dataRowCollectionFactory;
        $this->entityFieldChoices = $entityFieldChoices;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $importFile = new SplFileInfo($this->importDirectory . $this->importConfig->getFileName());
        $dataRowCollection = $this->dataRowCollectionFactory->buildFromFile($importFile, 1);

        // Getting the number of cells in the first row
        $rowSize = count($dataRowCollection->offsetGet(0));

        $data = [
            'csv' => $this->importConfig->getFileName(),
            'entity' => $this->importConfig->getEntityType(),
            'iso_lang' => $this->importConfig->getLanguageIso(),
            'separator' => $this->importConfig->getSeparator(),
            'multiple_value_separator' => $this->importConfig->getMultipleValueSeparator(),
            'truncate' => $this->importConfig->truncate(),
            'regenerate' => $this->importConfig->skipThumbnailRegeneration(),
            'match_ref' => $this->importConfig->matchReferences(),
            'forceIDs' => $this->importConfig->forceIds(),
            'sendemail' => $this->importConfig->sendEmail(),
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

        if (empty($data['csv'])) {
            $errors[] = [
                'key' => 'To proceed, please upload a file first.',
                'domain' => 'Admin.Advparameters.Notification',
                'parameters' => [],
            ];
        }

        return $errors;
    }
}
