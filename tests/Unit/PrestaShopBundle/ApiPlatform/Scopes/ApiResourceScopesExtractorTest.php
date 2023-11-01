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

namespace Tests\Unit\PrestaShopBundle\ApiPlatform\Scopes;

use ApiPlatform\Metadata\Resource\Factory\AttributesResourceMetadataCollectionFactory;
use PHPUnit\Framework\TestCase;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopes;
use PrestaShopBundle\ApiPlatform\Scopes\ApiResourceScopesExtractor;

class ApiResourceScopesExtractorTest extends TestCase
{
    private string $moduleDir;

    private string $projectDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->moduleDir = __DIR__ . '/../../../Resources/api_platform/fake_module_resources/';
        $this->projectDir = __DIR__ . '/../../../Resources/api_platform/fake_core_resources';
    }

    public function testGetAllResourceScopes(): void
    {
        $scopesExtractor = $this->buildExtractor();
        $resourceScopes = $scopesExtractor->getAllApiResourceScopes();

        $expectedResourceScopes = [
            ApiResourceScopes::createCoreScopes(['hook_read', 'hook_write']),
            ApiResourceScopes::createModuleScopes(['api_access_read'], 'fake_module'),
            ApiResourceScopes::createModuleScopes(['customer_group_read'], 'disabled_fake_module'),
        ];
        $this->assertEquals($expectedResourceScopes, $resourceScopes);
    }

    public function testGetEnabledResourceScopes(): void
    {
        $scopesExtractor = $this->buildExtractor();
        $resourceScopes = $scopesExtractor->getEnabledApiResourceScopes();

        $expectedResourceScopes = [
            ApiResourceScopes::createCoreScopes(['hook_read', 'hook_write']),
            ApiResourceScopes::createModuleScopes(['api_access_read'], 'fake_module'),
        ];
        $this->assertEquals($expectedResourceScopes, $resourceScopes);
    }

    private function buildExtractor(): ApiResourceScopesExtractor
    {
        return new ApiResourceScopesExtractor(
            new AttributesResourceMetadataCollectionFactory(),
            $this->moduleDir,
            ['fake_module', 'disabled_fake_module'],
            ['fake_module'],
            $this->projectDir
        );
    }
}
