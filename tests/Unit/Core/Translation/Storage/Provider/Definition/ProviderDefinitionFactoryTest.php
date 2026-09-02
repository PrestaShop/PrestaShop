<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
