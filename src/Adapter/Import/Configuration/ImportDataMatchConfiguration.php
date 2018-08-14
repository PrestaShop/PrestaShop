<?php


namespace PrestaShop\PrestaShop\Adapter\Import\Configuration;

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
        if (!isset($configuration['data_matching_configuration']) || !$configuration['data_matching_configuration']) {
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
     */
    private function saveConfigurationMatch(array $configuration)
    {
    }
}
