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

namespace PrestaShop\PrestaShop\Adapter\AuthorizationServer;

use PrestaShop\PrestaShop\Adapter\Cache\Clearer\SymfonyCacheClearer;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AuthorizationServerConfiguration implements DataConfigurationInterface
{
    public function __construct(
        private readonly Configuration $configuration,
        private readonly TranslatorInterface $translator,
        private readonly SymfonyCacheClearer $cacheClearer,
    ) {
    }

    public function getConfiguration()
    {
        return [
            'enable_admin_api' => $this->configuration->getBoolean('PS_ENABLE_ADMIN_API'),
            'enable_experimental_endpoints' => $this->configuration->getBoolean('PS_ENABLE_EXPERIMENTAL_API_ENDPOINTS'),
        ];
    }

    public function updateConfiguration(array $configuration)
    {
        $errors = $this->getConfigurationErrors($configuration);
        if (!empty($errors)) {
            return $errors;
        }

        $this->configuration->set('PS_ENABLE_ADMIN_API', $configuration['enable_admin_api']);
        $this->configuration->set('PS_ENABLE_EXPERIMENTAL_API_ENDPOINTS', $configuration['enable_experimental_endpoints']);

        // Clear cache so that Swagger and roles extraction are refreshed
        $this->cacheClearer->clear();

        return [];
    }

    public function validateConfiguration(array $configuration)
    {
        return empty($this->getConfigurationErrors($configuration));
    }

    private function getConfigurationErrors(array $configuration): array
    {
        $errors = [];
        if (!is_bool($configuration['enable_admin_api'])) {
            $errors[] = $this->translator->trans(
                'The %s field is invalid.',
                [$this->translator->trans('Admin API', [], 'Admin.Advparameters.Feature')],
                'Admin.Notifications.Error'
            );
        }
        if (!is_bool($configuration['enable_experimental_endpoints'])) {
            $errors[] = $this->translator->trans(
                'The %s field is invalid.',
                [$this->translator->trans('Enable experimental endpoints', [], 'Admin.Advparameters.Feature')],
                'Admin.Notifications.Error'
            );
        }

        return $errors;
    }
}
