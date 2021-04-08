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

namespace Tests\Unit\Core\Translation\Storage\Provider\Definition;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\BackofficeProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\CoreDomainProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\FrontofficeProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\MailsBodyProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\MailsProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ModuleProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\OthersProviderDefinition;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ProviderDefinitionFactory;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ProviderDefinitionInterface;
use PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition\ThemeProviderDefinition;
use RuntimeException;

class ProviderDefinitionFactoryTest extends TestCase
{
    public function testBuild(): void
    {
        $factory = new ProviderDefinitionFactory();
        $definitions = [
            ProviderDefinitionInterface::TYPE_MODULES => ModuleProviderDefinition::class,
            ProviderDefinitionInterface::TYPE_THEMES => ThemeProviderDefinition::class,
            ProviderDefinitionInterface::TYPE_CORE_DOMAIN => CoreDomainProviderDefinition::class,
            ProviderDefinitionInterface::TYPE_BACK => BackofficeProviderDefinition::class,
            ProviderDefinitionInterface::TYPE_FRONT => FrontofficeProviderDefinition::class,
            ProviderDefinitionInterface::TYPE_MAILS => MailsProviderDefinition::class,
            ProviderDefinitionInterface::TYPE_MAILS_BODY => MailsBodyProviderDefinition::class,
            ProviderDefinitionInterface::TYPE_OTHERS => OthersProviderDefinition::class,
        ];

        foreach ($definitions as $type => $definitionClass) {
            $this->assertInstanceOf($definitionClass, $factory->build($type, 'whatever'));
        }
    }

    public function testBuildThrowsExceptionIfTypeNotKnown(): void
    {
        $factory = new ProviderDefinitionFactory();
        $this->expectException(RuntimeException::class);
        $factory->build('fakeType');
    }
}
