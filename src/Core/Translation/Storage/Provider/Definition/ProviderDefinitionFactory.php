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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
