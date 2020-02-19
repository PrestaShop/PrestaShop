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

use Doctrine\Common\Inflector\Inflector;
use PrestaShop\PrestaShop\Core\Addon\Module\ModulePreloaderInterface;
use PrestaShop\PrestaShop\Core\Exception\FileNotFoundException;
use PrestaShop\PrestaShop\Core\Util\Exception\NamespaceNotFoundException;
use PrestaShopBundle\Service\Database\DoctrineRuntimeEntityMapper;

/**
 * Class ModuleEntityPreloader preloads module's doctrine entities.
 */
class ModuleEntityPreloader implements ModulePreloaderInterface
{
    /**
     * @var DoctrineRuntimeEntityMapper
     */
    private $entityMapper;

    /**
     * @var string
     */
    private $modulesFolder;

    /**
     * @param DoctrineRuntimeEntityMapper $entityMapper
     * @param string $modulesFolder
     */
    public function __construct(
        DoctrineRuntimeEntityMapper $entityMapper,
        string $modulesFolder
    ) {
        $this->entityMapper = $entityMapper;
        $this->modulesFolder = $modulesFolder;
    }

    /**
     * {@inheritdoc}
     */
    public function preload(string $moduleName): void
    {
        $moduleAlias = 'Module' . Inflector::camelize($moduleName);
        $entityFolder = realpath($this->modulesFolder . '/' . $moduleName . '/src/Entity');
        if (!is_dir($entityFolder)) {
            return;
        }

        try {
            $this->entityMapper->addDoctrineMapping($entityFolder, $moduleAlias);
        } catch (NamespaceNotFoundException $e) {
            // Do nothing, the module has no namespace
        } catch (FileNotFoundException $e) {
            // Do nothing, the module has no entity folder (already checked, but better safe than sorry)
        }
    }
}
