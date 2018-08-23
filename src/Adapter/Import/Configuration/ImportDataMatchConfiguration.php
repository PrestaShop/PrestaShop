<?php


namespace PrestaShop\PrestaShop\Adapter\Import\Configuration;

use Db;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
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

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getConfiguration()
    {
        // TODO: Implement getConfiguration() method.
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
}
