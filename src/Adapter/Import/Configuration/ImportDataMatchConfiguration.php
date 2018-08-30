<?php


namespace PrestaShop\PrestaShop\Adapter\Import\Configuration;

use Db;
use DbQuery;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\Factory\DataRowCollectionFactoryInterface;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use SplFileInfo;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ImportDataMatchConfiguration is responsible for saving and loading configuration of import step 2
 */
class ImportDataMatchConfiguration implements DataConfigurationInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $entityFieldChoices;

    /**
     * @var string path to the import file
     */
    private $importFilePath;

    /**
     * @var DataRowCollectionFactoryInterface
     */
    private $dataRowCollectionFactory;

    /**
     * @var ImportDirectory
     */
    private $importDirectory;

    /**
     * @var string file name of the imported file
     */
    private $importFilename;

    /**
     * @param TranslatorInterface $translator
     * @param array $entityFieldChoices
     * @param ImportDirectory $importDirectory
     * @param string $importFilename
     * @param DataRowCollectionFactoryInterface $dataRowCollectionFactory
     */
    public function __construct(
        TranslatorInterface $translator,
        array $entityFieldChoices,
        ImportDirectory $importDirectory,
        $importFilename,
        DataRowCollectionFactoryInterface $dataRowCollectionFactory
    ) {
        $this->translator = $translator;
        $this->entityFieldChoices = $entityFieldChoices;
        $this->dataRowCollectionFactory = $dataRowCollectionFactory;
        $this->importDirectory = $importDirectory;
        $this->importFilename = $importFilename;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $importFile = new SplFileInfo($this->importDirectory.$this->importFilename);
        $dataRowCollection = $this->dataRowCollectionFactory->buildFromFile($importFile, 1);
        $rowSize = 0;

        foreach ($dataRowCollection as $dataRow) {
            // Getting the number of cells in a row
            $rowSize = count($dataRow);
            break;
        }

        $configuration = [
            'type_value' => [],
        ];

        $numberOfValuesAdded = 0;

        foreach ($this->entityFieldChoices as $choice) {
            // If we already added the required number of values - stop adding them
            if ($numberOfValuesAdded >= $rowSize) {
                break;
            }

            $configuration['type_value'][] = $choice;
            $numberOfValuesAdded++;
        }

        return $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = $this->validateConfiguration($configuration);

        if (empty($errors)) {
            $this->saveConfigurationMatch($configuration);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $errors = [];
        if (!isset($configuration['match_name']) || !$configuration['match_name']) {
            $errors[] = $this->translator->trans(
                'Please name your data matching configuration in order to save it.',
                [],
                'Admin.Advparameters.Feature'
            );
        }

        if ($this->configurationNameExists($configuration['match_name'])) {
            $errors[] = $this->translator->trans(
                'This name already exists.',
                [],
                'Admin.Design.Notification'
            );
        }

        return $errors;
    }

    /**
     * Saves the import configuration match data
     *
     * @param array $configuration
     *
     * @throws \PrestaShopDatabaseException
     */
    private function saveConfigurationMatch(array $configuration)
    {
        Db::getInstance()->insert(
            'import_match',
            [
                'name' => pSQL($configuration['match_name']),
                'match' => '', //todo : pSQL($configuration['match']),
                'skip' => (int) $configuration['rows_skip']
            ],
            false,
            true,
            Db::INSERT_IGNORE
        );
    }

    /**
     * Checks if the configuration is already saved with the same name
     *
     * @param string $matchName
     *
     * @return bool
     */
    private function configurationNameExists($matchName)
    {
        $query = new DbQuery();
        $query->select('`id_import_match`');
        $query->from('import_match');
        $query->where('`name`="'.pSQL($matchName).'"');
        $result = Db::getInstance()->getValue($query);

        return $result ? true : false;
    }
}
