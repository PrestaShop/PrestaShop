<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Configuration;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopLogger;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class will manage Logs configuration for a Shop.
 */
class DatabaseLogsConfiguration implements DataConfigurationInterface
{
    public function __construct(
        private ConfigurationInterface $configuration
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $duplicateCheckLimit = $this->configuration->get(PrestaShopLogger::DUPLICATE_CHECK_LIMIT_CONFIGURATION);

        return [
            'database_min_logger_level' => (int) $this->configuration->get('PS_MIN_LOGGER_LEVEL_IN_DB'),
            'database_duplicate_check_limit' => is_numeric($duplicateCheckLimit)
                ? (int) $duplicateCheckLimit
                : PrestaShopLogger::DEFAULT_DUPLICATE_CHECK_LIMIT,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_MIN_LOGGER_LEVEL_IN_DB', (int) $configuration['database_min_logger_level']);

            if (isset($configuration['database_duplicate_check_limit'])) {
                $this->configuration->set(
                    PrestaShopLogger::DUPLICATE_CHECK_LIMIT_CONFIGURATION,
                    (int) $configuration['database_duplicate_check_limit']
                );
            }
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver
            ->setRequired(['database_min_logger_level'])
            ->setDefined(['database_duplicate_check_limit'])
            ->resolve($configuration);

        return true;
    }
}
