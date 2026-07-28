<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Search\Configuration;

use Exception;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

class IndexationConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = [
        'indexing',
    ];

    /**
     * @return array<string, mixed>
     */
    public function getConfiguration(): array
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'indexing' => (bool) $this->configuration->get('PS_SEARCH_INDEXATION', false, $shopConstraint),
        ];
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     *
     * @throws Exception
     */
    public function updateConfiguration(array $config): array
    {
        if ($this->validateConfiguration($config)) {
            $shopConstraint = $this->getShopConstraint();

            $this->updateConfigurationValue('PS_SEARCH_INDEXATION', 'indexing', $config, $shopConstraint);
        }

        return [];
    }

    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('indexing', 'bool');
    }
}
