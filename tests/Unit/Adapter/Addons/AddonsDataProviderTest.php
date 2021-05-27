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

namespace Tests\Unit\Adapter\Addons;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Addons\AddonsDataProvider;
use PrestaShop\PrestaShop\Adapter\Module\ModuleZipManager;
use PrestaShopBundle\Service\DataProvider\Marketplace\ApiClient;

class AddonsDataProviderTest extends TestCase
{
    public function testRequestModule(): void
    {
        $moduleId = 12345678;

        $apiClient = $this->createMock(ApiClient::class);
        $apiClient->expects($this->once())->method('getModuleZip')->with($moduleId, 'stable');

        $addonsDataProvider = new AddonsDataProvider($apiClient, $this->getModuleZipManager());

        $addonsDataProvider->request('module_download', [
            'id_module' => $moduleId,
        ]);
    }

    public function testRequestModuleWithModuleChannel(): void
    {
        $moduleId = 12345678;
        $moduleChannel = 'azerty';

        $apiClient = $this->createMock(ApiClient::class);
        $apiClient->expects($this->once())->method('getModuleZip')->with($moduleId, $moduleChannel);

        $addonsDataProvider = new AddonsDataProvider($apiClient, $this->getModuleZipManager(), $moduleChannel);

        $addonsDataProvider->request('module_download', [
            'id_module' => $moduleId,
        ]);
    }

    public function testRequestModuleConnected(): void
    {
        $moduleId = 12345678;
        $addonsUsername = $_COOKIE['username_addons'] = 'username_addons';
        $addonsPassword = $_COOKIE['password_addons'] = 'password_addons';

        $apiClient = $this->createMock(ApiClient::class);
        $apiClient->expects($this->once())->method('setUserMail')->with($addonsUsername)->willReturnSelf();
        $apiClient->expects($this->once())->method('setPassword')->with($addonsPassword)->willReturnSelf();
        $apiClient->expects($this->once())->method('getModuleZip')->with($moduleId, 'stable');

        $addonsDataProvider = new AddonsDataProvider($apiClient, $this->getModuleZipManager());

        $addonsDataProvider->request('module_download', [
            'id_module' => $moduleId,
            'username_addons' => $addonsUsername,
            'password_addons' => $addonsPassword,
        ]);
    }

    public function testRequestModuleConnectedWithModuleChannel(): void
    {
        $moduleId = 12345678;
        $moduleChannel = 'azerty';
        $addonsUsername = $_COOKIE['username_addons'] = 'username_addons';
        $addonsPassword = $_COOKIE['password_addons'] = 'password_addons';

        $apiClient = $this->createMock(ApiClient::class);
        $apiClient->expects($this->once())->method('setUserMail')->with($addonsUsername)->willReturnSelf();
        $apiClient->expects($this->once())->method('setPassword')->with($addonsPassword)->willReturnSelf();
        $apiClient->expects($this->once())->method('getModuleZip')->with($moduleId, $moduleChannel);

        $addonsDataProvider = new AddonsDataProvider($apiClient, $this->getModuleZipManager(), $moduleChannel);

        $addonsDataProvider->request('module_download', [
            'id_module' => $moduleId,
            'username_addons' => $addonsUsername,
            'password_addons' => $addonsPassword,
        ]);
    }

    private function getModuleZipManager(): ModuleZipManager
    {
        return $this->createMock(ModuleZipManager::class);
    }
}
