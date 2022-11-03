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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\CommandHandler;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Command\SaveSqlRequestSettingsCommand;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestSettings;
use PrestaShop\PrestaShop\Core\Encoding\CharsetEncoding;

/**
 * Class SaveSqlRequestSettingsHandler handles command to save SqlRequest settings.
 */
final class SaveSqlRequestSettingsHandler implements SaveSqlRequestSettingsHandlerInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(SaveSqlRequestSettingsCommand $command)
    {
        $this->configuration->set(SqlRequestSettings::FILE_ENCODING, $this->getEncodingFileValue($command));
    }

    /**
     * File encodings are saved as integer values in databases.
     *
     * @param SaveSqlRequestSettingsCommand $command
     *
     * @return int
     */
    private function getEncodingFileValue(SaveSqlRequestSettingsCommand $command)
    {
        $valuesMapping = [
            CharsetEncoding::UTF_8 => 1,
            CharsetEncoding::ISO_8859_1 => 2,
        ];

        return $valuesMapping[$command->getFileEncoding()];
    }
}
