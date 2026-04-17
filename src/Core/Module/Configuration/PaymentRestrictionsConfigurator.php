<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Module\Configuration;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Module\DataProvider\PaymentModuleListProviderInterface;

/**
 * Class PaymentRestrictionsConfigurator is responsible for configuring restrictions for payment modules.
 */
final class PaymentRestrictionsConfigurator implements PaymentRestrictionsConfiguratorInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var string
     */
    private $databasePrefix;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @var PaymentModuleListProviderInterface
     */
    private $paymentModuleProvider;

    /**
     * @param Connection $connection
     * @param string $databasePrefix
     * @param int $shopId
     * @param PaymentModuleListProviderInterface $paymentModuleProvider
     */
    public function __construct(
        Connection $connection,
        $databasePrefix,
        $shopId,
        PaymentModuleListProviderInterface $paymentModuleProvider
    ) {
        $this->connection = $connection;
        $this->databasePrefix = $databasePrefix;
        $this->shopId = $shopId;
        $this->paymentModuleProvider = $paymentModuleProvider;
    }

    /**
     * @param array $currencyRestrictions
     *
     * @return bool|void
     */
    public function configureCurrencyRestrictions(array $currencyRestrictions)
    {
        $this->configureRestrictions('currency', $currencyRestrictions);
    }

    /**
     * @param array $countryRestrictions
     *
     * @return bool|void
     */
    public function configureCountryRestrictions(array $countryRestrictions)
    {
        $this->configureRestrictions('country', $countryRestrictions);
    }

    /**
     * @param array $groupRestrictions
     *
     * @return bool|void
     */
    public function configureGroupRestrictions(array $groupRestrictions)
    {
        $this->configureRestrictions('group', $groupRestrictions);
    }

    /**
     * @param array $carrierRestrictions
     *
     * @return bool|void
     */
    public function configureCarrierRestrictions(array $carrierRestrictions)
    {
        $this->configureRestrictions('carrier', $carrierRestrictions);
    }

    /**
     * @param string $restrictionType
     * @param array $restrictions
     */
    private function configureRestrictions($restrictionType, array $restrictions)
    {
        [$moduleIds, $newConfiguration] = $this->parseRestrictionData($restrictions);

        $this->clearCurrentConfiguration($restrictionType, $moduleIds);
        $this->insertNewConfiguration($restrictionType, $newConfiguration);
    }

    /**
     * Clear current configuration for given restriction type.
     *
     * @param string $restrictionType
     * @param int[] $moduleIds
     *
     * @return int
     */
    private function clearCurrentConfiguration($restrictionType, array $moduleIds)
    {
        $clearSql = '
            DELETE FROM ' . $this->getTableNameForRestriction($restrictionType) . '
            WHERE id_shop = ' . (int) $this->shopId . ' AND id_module IN (' . implode(',', array_map('intval', $moduleIds)) . ')
        ';

        return $this->connection->executeUpdate($clearSql);
    }

    /**
     * Insert new configuration for given restriction type.
     *
     * @param string $restrictionType
     * @param array $newConfiguration
     */
    private function insertNewConfiguration($restrictionType, $newConfiguration)
    {
        if (!empty($newConfiguration)) {
            $fieldName = 'carrier' === $restrictionType ? 'reference' : $restrictionType;

            $this->connection->executeUpdate('
                INSERT INTO `' . $this->getTableNameForRestriction($restrictionType) . '`
                (`id_module`, `id_shop`, `id_' . $fieldName . '`)
                VALUES ' . implode(',', $newConfiguration));
        }
    }

    /**
     * Get table name for module restrictions.
     *
     * @param string $restrictionType
     *
     * @return string
     */
    private function getTableNameForRestriction($restrictionType)
    {
        return $this->databasePrefix . 'module_' . $restrictionType;
    }

    /**
     * Parse data from restrictions.
     *
     * @param array $restrictions
     *
     * @return array
     */
    private function parseRestrictionData(array $restrictions)
    {
        $moduleIds = [];
        $insertValues = [];

        $paymentModules = $this->paymentModuleProvider->getPaymentModuleList();

        foreach ($restrictions as $moduleName => $restriction) {
            if (isset($paymentModules[$moduleName])) {
                $moduleId = $paymentModules[$moduleName]->database->get('id');

                $moduleIds[] = $moduleId;

                if (!is_array($restriction)) {
                    $restriction = [$restriction];
                }

                foreach ($restriction as $restrictionValues) {
                    $insertValues[] = '(' . (int) $moduleId . ', ' . (int) $this->shopId . ', ' . (int) $restrictionValues . ')';
                }
            }
        }

        return [
            $moduleIds,
            $insertValues,
        ];
    }
}
