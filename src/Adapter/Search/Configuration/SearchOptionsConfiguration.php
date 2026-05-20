<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Search\Configuration;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

class SearchOptionsConfiguration implements DataConfigurationInterface
{
    public function __construct(private readonly Configuration $configuration)
    {
    }

    public function getConfiguration(): array
    {
        return [
            'search_within_word' => $this->configuration->getBoolean('PS_SEARCH_START'),
            'search_exact_end_match' => $this->configuration->getBoolean('PS_SEARCH_END'),
            'fuzzy_search' => $this->configuration->getBoolean('PS_SEARCH_FUZZY'),
            'fuzzy_max_words' => $this->configuration->get('PS_SEARCH_FUZZY_MAX_LOOP'),
            'fuzzy_max_difference' => $this->configuration->get('PS_SEARCH_FUZZY_MAX_DIFFERENCE'),
            'max_word_length' => $this->configuration->get('PS_SEARCH_MAX_WORD_LENGTH'),
            'min_word_length' => $this->configuration->get('PS_SEARCH_MINWORDLEN'),
            'blacklisted_words' => $this->configuration->get('PS_SEARCH_BLACKLIST'),
        ];
    }

    public function updateConfiguration(array $config): array
    {
        $this->configuration->set('PS_SEARCH_START', (int) $config['search_within_word']);
        $this->configuration->set('PS_SEARCH_END', (int) $config['search_exact_end_match']);
        $this->configuration->set('PS_SEARCH_FUZZY', (int) $config['fuzzy_search']);
        $this->configuration->set('PS_SEARCH_FUZZY_MAX_LOOP', (int) $config['fuzzy_max_words']);
        $this->configuration->set('PS_SEARCH_FUZZY_MAX_DIFFERENCE', (int) $config['fuzzy_max_difference']);
        $this->configuration->set('PS_SEARCH_MAX_WORD_LENGTH', (int) $config['max_word_length']);
        $this->configuration->set('PS_SEARCH_MINWORDLEN', (int) $config['min_word_length']);
        $this->configuration->set('PS_SEARCH_BLACKLIST', $config['blacklisted_words']);

        return [];
    }

    public function validateConfiguration(array $config): bool
    {
        return true;
    }
}
