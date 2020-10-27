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

namespace Tests\Resources;

use Symfony\Component\Filesystem\Filesystem;

/**
 * @todo: better name?
 */
class ResourceResetter
{
    /**
     * Name for directory of test images in system tmp dir
     */
    const BACKUP_TEST_IMG_DIR = 'ps_backup_test_img';

    /**
     * Backs up test images directory to allow resetting their original state later in tests
     */
    public static function backupImages(): void
    {
        (new Filesystem())->mirror(_PS_IMG_DIR_, static::getBackupTestImgDir());
    }

    /**
     * Resets test images directory to initial state
     */
    public static function resetImages(): void
    {
        (new Filesystem())->mirror(static::getBackupTestImgDir(), _PS_IMG_DIR_);
    }

    /**
     * Provide test img directory path, in which initial dummy images state should be saved
     *
     * @return string
     */
    public static function getBackupTestImgDir(): string
    {
        return sys_get_temp_dir() . DIRECTORY_SEPARATOR . static::BACKUP_TEST_IMG_DIR;
    }
}
