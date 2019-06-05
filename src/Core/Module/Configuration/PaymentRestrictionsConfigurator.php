<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
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
     * {@inheritdoc}
     */
    public function configureCurrencyRestrictions(array $currencyRestrictions)
    {
        $this->configureRestrictions('currency', $currencyRestrictions);
    }

    /**
     * {@inheritdoc}
     */
    public function configureCountryRestrictions(array $countryRestrictions)
    {
        $this->configureRestrictions('country', $countryRestrictions);
    }

    /**
     * {@inheritdoc}
     */
    public function configureGroupRestrictions(array $groupRestrictions)
    {
        $this->configureRestrictions('group', $groupRestrictions);
    }

    /**
     * {@inheritdoc}
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
        list($moduleIds, $newConfiguration) = $this->parseRestrictionData($restrictions);

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
