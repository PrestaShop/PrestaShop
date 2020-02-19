<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Module\Preloader;

use PrestaShop\PrestaShop\Core\Addon\Module\ModulePreloaderInterface;

/**
 * Class ModuleAutoloadFilePreloader loads module's autoload file if present.
 */
class ModuleAutoloadFilePreloader implements ModulePreloaderInterface
{
    /**
     * @var string
     */
    private $modulesFolder;

    /**
     * @param string $modulesFolder
     */
    public function __construct(string $modulesFolder)
    {
        $this->modulesFolder = $modulesFolder;
    }

    /**
     * {@inheritdoc}
     */
    public function preload(string $moduleName): void
    {
        $autoloadFile = $this->modulesFolder . '/' . $moduleName . '/vendor/autoload.php';
        if (file_exists($autoloadFile)) {
            include_once $autoloadFile;
        }
    }
}
