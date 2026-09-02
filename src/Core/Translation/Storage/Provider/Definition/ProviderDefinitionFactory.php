<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Storage\Provider\Definition;

use RuntimeException;

class ProviderDefinitionFactory
{
    public function build(
        string $type,
        ?string $selectedValue = null
    ): ProviderDefinitionInterface {
        switch ($type) {
            case ProviderDefinitionInterface::TYPE_MODULES:
                return new ModuleProviderDefinition($selectedValue);
            case ProviderDefinitionInterface::TYPE_THEMES:
                return new ThemeProviderDefinition($selectedValue);
            case ProviderDefinitionInterface::TYPE_CORE_DOMAIN:
                return new CoreDomainProviderDefinition($selectedValue);
            case ProviderDefinitionInterface::TYPE_BACK:
                return new BackofficeProviderDefinition();
            case ProviderDefinitionInterface::TYPE_FRONT:
                return new FrontofficeProviderDefinition();
            case ProviderDefinitionInterface::TYPE_MAILS:
                return new MailsProviderDefinition();
            case ProviderDefinitionInterface::TYPE_MAILS_BODY:
                return new MailsBodyProviderDefinition();
            case ProviderDefinitionInterface::TYPE_OTHERS:
                return new OthersProviderDefinition();
            default:
                throw new RuntimeException(sprintf('Unrecognized type: %s', $type));
        }
    }
}
