<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Translation\Builder\Map;

/**
 * This class is the representation of a translations catalogue.
 * A catalogue is composed by domains and theirs subdomains which have messages with 3 layers of translations.
 * We have methods to get the catalogue as and array or just get the tree,
 * both with overall and domains metadata having the messages count and untranslated messages count.
 */
class Catalogue
{
    public const METADATA_KEY_NAME = '__metadata';
    public const EMPTY_META = [
        'count' => 0,
        'missing_translations' => 0,
    ];

    /**
     * @var Domain[]
     */
    private $domains = [];

    /**
     * @param Domain $domain
     *
     * @return Catalogue
     */
    public function addDomain(Domain $domain): self
    {
        if (!array_key_exists($domain->getDomainName(), $this->domains)) {
            $this->domains[$domain->getDomainName()] = $domain;
        }

        return $this;
    }

    /** Returns a single Domain DTO.
     *
     * @param string $domainName
     *
     * @return Domain|null
     */
    public function getDomain(string $domainName): ?Domain
    {
        if (array_key_exists($domainName, $this->domains)) {
            return $this->domains[$domainName];
        }

        return null;
    }

    /**
     * @return Domain[]
     */
    public function getDomains(): array
    {
        return $this->domains;
    }

    public function getTranslationsCount(): int
    {
        return array_reduce($this->domains, function ($carry, $domain) {
            return $carry + $domain->getTranslationsCount();
        }, 0);
    }

    public function getMissingTranslationsCount(): int
    {
        return array_reduce($this->domains, function ($carry, $domain) {
            return $carry + $domain->getMissingTranslationsCount();
        }, 0);
    }

    /**
     * @param bool $withMetadata
     *
     * @return array
     */
    public function toArray(bool $withMetadata = true): array
    {
        $data = [];
        foreach ($this->domains as $domain) {
            $data[$domain->getDomainName()] = $domain->toArray($withMetadata);
        }

        if ($withMetadata) {
            $data[self::METADATA_KEY_NAME] = [
                'count' => count($this->domains),
                'missing_translations' => $this->getMissingTranslationsCount(),
            ];
        }

        ksort($data);

        return $data;
    }

    public function buildTree(): array
    {
        // template for initializing metadata
        $tree = [
            self::METADATA_KEY_NAME => self::EMPTY_META,
        ];
        foreach ($this->domains as $domain) {
            $domain->mergeTree($tree);
        }

        $this->updateCounters($tree);

        return $tree;
    }

    /**
     * Updates counters of this subtree by adding the sum of children's counters
     *
     * @param array $subtree
     *
     * @return array Array of [sum of count, sum of missing_translations]
     */
    private function updateCounters(array &$subtree): array
    {
        foreach ($subtree as $key => $values) {
            if ($key === self::METADATA_KEY_NAME) {
                continue;
            }

            // update child and get its counters
            [$count, $missing] = $this->updateCounters($subtree[$key]);

            // update this tree's counters by adding the child's
            $subtree[self::METADATA_KEY_NAME]['count'] += $count;
            $subtree[self::METADATA_KEY_NAME]['missing_translations'] += $missing;
        }

        return [
            $subtree[self::METADATA_KEY_NAME]['count'],
            $subtree[self::METADATA_KEY_NAME]['missing_translations'],
        ];
    }
}
