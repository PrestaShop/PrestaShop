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

namespace PrestaShop\PrestaShop\Core\Configuration;

use SplFileInfo;
use Symfony\Component\Finder\Finder;

class AdminFolderFinder
{
     /*
      * Look for potential admin folders, condition to meet:
      * First level folders in the project folder contains a PHP file that define the const PS_ADMIN_DIR or _PS_ADMIN_DIR_
      * The first folder found is used (alphabetical order, but files named index.php have the highest priority)
      */
    static function findAdminFolder(string $projectDir): Finder
    {
        $finder = new Finder();

        $finder->files()
            ->name('*.php')
            ->contains('/define\([\'\"](_)?PS_ADMIN_DIR(_)?[\'\"]/')
            ->depth('== 1')
            ->sort(function (SplFileInfo $a, SplFileInfo $b): int {
                // Prioritize files named index.php
                if ($a->getFilename() === 'index.php') {
                    return -1;
                }

                return strcmp($a->getRealPath(), $b->getRealPath());
            })
            ->in($projectDir)
        ;

        return $finder;
    }
}
