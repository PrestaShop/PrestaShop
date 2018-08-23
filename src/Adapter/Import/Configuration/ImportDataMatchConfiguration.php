<?php


namespace PrestaShop\PrestaShop\Adapter\Import\Configuration;

use Db;
use DbQuery;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class ImportDataMatchConfiguration is responsible for savi
 */
class ImportDataMatchConfiguration implements DataConfigurationInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * ImportDataMatchConfiguration constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getConfiguration()
    {
        return [];
    }

    public function updateConfiguration(array $configuration)
    {
        $errors = $this->validateConfiguration($configuration);

        if (empty($errors)) {
            $this->saveConfigurationMatch($configuration);
        }

        return $errors;
    }

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

        if ($this->isConfigurationNameExists($configuration['match_name'])) {
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
    private function isConfigurationNameExists($matchName)
    {
        $query = new DbQuery();
        $query->select('`id_import_match`');
        $query->from('import_match');
        $query->where('`name`="'.pSQL($matchName).'"');
        $result = Db::getInstance()->getValue($query);

        return $result ? true : false;
    }
}
