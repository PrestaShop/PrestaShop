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

class SearchOptionsConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = [
        'search_within_word',
        'search_exact_end_match',
        'fuzzy_search',
        'fuzzy_max_words',
        'fuzzy_max_difference',
        'max_word_length',
        'min_word_length',
        'blacklisted_words',
    ];

    /**
     * @return array<string, mixed>
     */
    public function getConfiguration(): array
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'search_within_word' => (bool) $this->configuration->get('PS_SEARCH_START', false, $shopConstraint),
            'search_exact_end_match' => (bool) $this->configuration->get('PS_SEARCH_END', false, $shopConstraint),
            'fuzzy_search' => (bool) $this->configuration->get('PS_SEARCH_FUZZY', false, $shopConstraint),
            'fuzzy_max_words' => (int) $this->configuration->get('PS_SEARCH_FUZZY_MAX_LOOP', 0, $shopConstraint),
            'fuzzy_max_difference' => (int) $this->configuration->get('PS_SEARCH_FUZZY_MAX_DIFFERENCE', 0, $shopConstraint),
            'max_word_length' => (int) $this->configuration->get('PS_SEARCH_MAX_WORD_LENGTH', 0, $shopConstraint),
            'min_word_length' => (int) $this->configuration->get('PS_SEARCH_MINWORDLEN', 0, $shopConstraint),
            'blacklisted_words' => $this->configuration->get('PS_SEARCH_BLACKLIST', null, $shopConstraint),
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

            $this->updateConfigurationValue('PS_SEARCH_START', 'search_within_word', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_SEARCH_END', 'search_exact_end_match', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_SEARCH_FUZZY', 'fuzzy_search', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_SEARCH_FUZZY_MAX_LOOP', 'fuzzy_max_words', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_SEARCH_FUZZY_MAX_DIFFERENCE', 'fuzzy_max_difference', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_SEARCH_MAX_WORD_LENGTH', 'max_word_length', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_SEARCH_MINWORDLEN', 'min_word_length', $config, $shopConstraint);
            $this->updateConfigurationValue('PS_SEARCH_BLACKLIST', 'blacklisted_words', $config, $shopConstraint);
        }

        return [];
    }

    protected function buildResolver(): OptionsResolver
    {
        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('search_within_word', 'bool')
            ->setAllowedTypes('search_exact_end_match', 'bool')
            ->setAllowedTypes('fuzzy_search', 'bool')
            ->setAllowedTypes('fuzzy_max_words', ['null', 'int'])
            ->setAllowedTypes('fuzzy_max_difference', ['null', 'int'])
            ->setAllowedTypes('max_word_length', 'int')
            ->setAllowedTypes('min_word_length', ['null', 'int'])
            ->setAllowedTypes('blacklisted_words', 'array');
    }
}
