<?php

namespace PrestaShopBundle\Form\Admin\AdvancedParameters\Import;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class ImportDataConfigurationProvider is responsible for providing or updating data for import match configuration
 */
final class ImportDataConfigurationProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $dataConfiguration;

    public function __construct(DataConfigurationInterface $dataConfiguration)
    {
        $this->dataConfiguration = $dataConfiguration;
    }

    public function getData()
    {
        return [
            'import_data_configuration' => $this->dataConfiguration->getConfiguration()
        ];
    }

    public function setData(array $data)
    {
        return $this->dataConfiguration->updateConfiguration($data);
    }
}
