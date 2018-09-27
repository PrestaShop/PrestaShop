<?php

namespace PrestaShop\PrestaShop\Adapter\Import;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Import\File\DataRow\Factory\DataRowCollectionFactoryInterface;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use PrestaShopBundle\Entity\Repository\ImportMatchRepository;
use SplFileInfo;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ImportDataMatchConfiguration is responsible for saving and loading configuration of import step 2.
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
     * @var DataRowCollectionFactoryInterface
     */
    private $dataRowCollectionFactory;

    /**
     * @var ImportDirectory
     */
    private $importDirectory;

    /**
     * @var Connection database connection
     */
    private $connection;

    /**
     * @var string database table prefix
     */
    private $dbPrefix;

    /**
     * @var ImportMatchRepository
     */
    private $importMatchRepository;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @param TranslatorInterface $translator
     * @param array $entityFieldChoices
     * @param ImportDirectory $importDirectory
     * @param DataRowCollectionFactoryInterface $dataRowCollectionFactory
     * @param Connection $connection
     * @param string $dbPrefix
     * @param ImportMatchRepository $importMatchRepository
     * @param SessionInterface $session
     */
    public function __construct(
        TranslatorInterface $translator,
        array $entityFieldChoices,
        ImportDirectory $importDirectory,
        DataRowCollectionFactoryInterface $dataRowCollectionFactory,
        Connection $connection,
        $dbPrefix,
        ImportMatchRepository $importMatchRepository,
        SessionInterface $session
    ) {
        $this->translator = $translator;
        $this->entityFieldChoices = $entityFieldChoices;
        $this->dataRowCollectionFactory = $dataRowCollectionFactory;
        $this->importDirectory = $importDirectory;
        $this->connection = $connection;
        $this->dbPrefix = $dbPrefix;
        $this->importMatchRepository = $importMatchRepository;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $importFile = new SplFileInfo($this->importDirectory . $this->session->get('csv'));
        $dataRowCollection = $this->dataRowCollectionFactory->buildFromFile($importFile, 1);

        // Getting the number of cells in the first row
        $rowSize = count($dataRowCollection->offsetGet(0));

        $configuration = [
            'type_value' => [],
            'entity' => $this->session->get('entity'),
            'truncate' => $this->session->get('truncate'),
            'match_ref' => $this->session->get('match_ref'),
            'regenerate' => $this->session->get('regenerate'),
            'forceIDs' => $this->session->get('forceIDs'),
            'sendemail' => $this->session->get('sendemail'),
            'csv' => $this->session->get('csv'),
            'separator' => $this->session->get('separator'),
            'multiple_value_separator' => $this->session->get('multiple_value_separator'),
            'iso_lang' => $this->session->get('iso_lang'),
        ];

        $numberOfValuesAdded = 0;

        // Add as many values to the configuration as the are cells in the row
        foreach ($this->entityFieldChoices as $choice) {
            // If we already added the required number of values - stop adding them
            if ($numberOfValuesAdded >= $rowSize) {
                break;
            }

            $configuration['type_value'][] = $choice;
            ++$numberOfValuesAdded;
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
     * Saves the import configuration match data.
     *
     * @param array $configuration
     */
    private function saveConfigurationMatch(array $configuration)
    {
        $this->connection->insert($this->dbPrefix . 'import_match',
            [
                '`name`' => $configuration['match_name'],
                '`match`' => implode('|', $configuration['type_value']),
                '`skip`' => $configuration['rows_skip'],
            ]
        );
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
        return $this->importMatchRepository->findOneByName($matchName) ? true : false;
    }
}
