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

declare(strict_types=1);

namespace PrestaShopBundle\Doctrine;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Platforms\MySQL57Platform;

/**
 * This wrapper connection class is used to prevent a bug in CLI when the shop is not configured yet.
 * The problem is that Doctrine warm up automatically tries to get the database driver version vy connecting
 * to it, but when no configuration is set yet the connection obviously fails.
 *
 * There is a possible solution to force server_version in the doctrine DBAL configuration but since PrestaShop
 * is a framework that can be installed with different database versions we can't hard-code it, besides forcing
 * the version disables this automatic version detection feature.
 *
 * (See https://github.com/doctrine/DoctrineBundle/issues/673 for more details)
 *
 * So this class acts as a fallback when the connection tries to get the database platform but fails we catch the
 * exception, we check if the parameters file has been configured. If it doesn't exist then we are in a case where
 * the automatic detection failure can be ignored, in other case we throw the caught exception and change nothing to
 * the original behaviour thus reducing the impact of this wrapper as much as possible.
 *
 * In the case we handle a fallback we use MySQL 5.7 as the default platform.
 */
class DatabaseConnection extends Connection
{
    private const PARAMETERS_FILE = __DIR__ . '/../../../app/config/parameters.php';

    public function getDatabasePlatform()
    {
        try {
            $detectedVersion = parent::getDatabasePlatform();
        } catch (Exception $e) {
            if (!file_exists(self::PARAMETERS_FILE)) {
                return new MySQL57Platform();
            }

            throw $e;
        }

        return $detectedVersion;
    }
}
