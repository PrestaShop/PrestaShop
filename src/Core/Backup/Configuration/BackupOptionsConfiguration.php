<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Backup\Configuration;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Class BackupOptionsConfigurator configures backup options.
 */
final class BackupOptionsConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'backup_all' => (bool) $this->configuration->get('PS_BACKUP_ALL'),
            'backup_drop_tables' => (bool) $this->configuration->get('PS_BACKUP_DROP_TABLE'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $config)
    {
        if ($this->validateConfiguration($config)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_BACKUP_ALL', 'backup_all', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_BACKUP_DROP_TABLE', 'backup_drop_tables', $config, $shopConstraint);
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $config)
    {
        return isset(
            $config['backup_all'],
            $config['backup_drop_tables']
        );
    }
}
