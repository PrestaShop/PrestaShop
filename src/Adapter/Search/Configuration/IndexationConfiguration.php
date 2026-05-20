<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Search\Configuration;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

class IndexationConfiguration implements DataConfigurationInterface
{
    public function __construct(private readonly Configuration $configuration)
    {
    }

    public function getConfiguration(): array
    {
        return [
            'indexing' => $this->configuration->getBoolean('PS_SEARCH_INDEXATION'),
        ];
    }

    public function updateConfiguration(array $config): array
    {
        $this->configuration->set('PS_SEARCH_INDEXATION', (int) $config['indexing']);

        return [];
    }

    public function validateConfiguration(array $config): bool
    {
        return true;
    }
}
