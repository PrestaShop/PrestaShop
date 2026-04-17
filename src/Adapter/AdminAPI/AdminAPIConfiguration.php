<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\AdminAPI;

use PrestaShop\PrestaShop\Adapter\Cache\Clearer\SymfonyCacheClearer;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class AdminAPIConfiguration implements DataConfigurationInterface
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
            'force_debug_secured' => $this->configuration->getBoolean('PS_ADMIN_API_FORCE_DEBUG_SECURED'),
        ];
    }

    public function updateConfiguration(array $configuration)
    {
        $errors = $this->getConfigurationErrors($configuration);
        if (!empty($errors)) {
            return $errors;
        }

        $this->configuration->set('PS_ENABLE_ADMIN_API', $configuration['enable_admin_api']);
        $this->configuration->set('PS_ADMIN_API_FORCE_DEBUG_SECURED', $configuration['force_debug_secured']);

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

        return $errors;
    }
}
