<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\SqlManagement\QueryHandler;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\Query\GetSqlRequestSettings;
use PrestaShop\PrestaShop\Core\Domain\SqlManagement\SqlRequestSettings;
use PrestaShop\PrestaShop\Core\Encoding\CharsetEncoding;

/**
 * Class GetSqlRequestSettingsHandler handles query to get SqlRequest settings.
 */
final class GetSqlRequestSettingsHandler implements GetSqlRequestSettingsHandlerInterface
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
    public function handle(GetSqlRequestSettings $query)
    {
        $fileEncodingIntValue = $this->configuration->get(SqlRequestSettings::FILE_ENCODING);

        return new SqlRequestSettings(
            $this->getFileEncoding($fileEncodingIntValue)
        );
    }

    /**
     * File encodings are saved as integer values in databases.
     *
     * @param null|int $rawValue
     *
     * @return string
     */
    private function getFileEncoding($rawValue)
    {
        $valuesMapping = array(
            1 => CharsetEncoding::UTF_8,
            2 => CharsetEncoding::ISO_8859_1,
        );

        if (isset($valuesMapping[$rawValue])) {
            return $valuesMapping[$rawValue];
        }

        return CharsetEncoding::UTF_8;
    }
}
