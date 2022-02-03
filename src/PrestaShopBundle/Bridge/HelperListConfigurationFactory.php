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

namespace PrestaShopBundle\Bridge;

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Build Helper list configuration
 */
class HelperListConfigurationFactory
{
    public function create(array $configuration = []): HelperListConfiguration
    {
        $helperListConfiguration = new HelperListConfiguration();

        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $configuration = $resolver->resolve($configuration);

        $helperListConfiguration->table = $configuration['table'];
        $helperListConfiguration->listId = $configuration['listId'];
        $helperListConfiguration->className = $configuration['className'];
        $helperListConfiguration->identifier = $configuration['identifier'];
        $helperListConfiguration->isJoinLanguageTableAuto = $configuration['isJoinLanguageTableAuto'];
        $helperListConfiguration->deleted = $configuration['deleted'];
        $helperListConfiguration->defaultOrderBy = $configuration['defaultOrderBy'];
        $helperListConfiguration->fieldsList = $configuration['fieldsList'];
        $helperListConfiguration->explicitSelect = $configuration['explicitSelect'];
        $helperListConfiguration->useFoundRows = $configuration['useFoundRows'];

        return $helperListConfiguration;
    }

    private function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('defaultOrderBy', function (Options $options) {
                return $options['identifier'];
            }
        );

        $resolver->setDefaults([
            'isJoinLanguageTableAuto' => false,
            'deleted' => false,
            'explicitSelect' => false,
            'useFoundRows' => true,
        ]);

        $resolver->setRequired([
            'table',
            'listId',
            'className',
            'identifier',
            'fieldsList',
        ]);
    }
}
