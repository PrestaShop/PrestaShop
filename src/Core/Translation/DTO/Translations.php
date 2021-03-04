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

namespace PrestaShop\PrestaShop\Core\Translation\DTO;

/**
 * This class is the representation of a translations catalogue.
 * A catalogue is composed by domains and theirs subdomains which have messages with 3 layers of translations.
 * We have methods to get the catalogue as and array or just get the tree,
 * both with overall and domains metadata having the messages count and untranslated messages count.
 */
class Translations
{
    public const METADATA_KEY_NAME = '__metadata';
    public const EMPTY_META = [
        'count' => 0,
        'missing_translations' => 0,
    ];

    /**
     * @var DomainTranslation[]
     */
    private $domainTranslations = [];

    /**
     * @param DomainTranslation $domainTranslations
     *
     * @return Translations
     */
    public function addDomainTranslation(DomainTranslation $domainTranslations): self
    {
        if (!array_key_exists($domainTranslations->getDomainName(), $this->domainTranslations)) {
            $this->domainTranslations[$domainTranslations->getDomainName()] = $domainTranslations;
        }

        return $this;
    }

    /** Returns a single DomainTranslation DTO.
     *
     * @param string $domainName
     *
     * @return DomainTranslation|null
     */
    public function getDomainTranslation(string $domainName): ?DomainTranslation
    {
        if (array_key_exists($domainName, $this->domainTranslations)) {
            return $this->domainTranslations[$domainName];
        }

        return null;
    }

    /**
     * @return DomainTranslation[]
     */
    public function getDomainTranslations(): array
    {
        return $this->domainTranslations;
    }

    public function getTranslationsCount(): int
    {
        return array_reduce($this->domainTranslations, function ($carry, $domainTranslation) {
            return $carry + $domainTranslation->getTranslationsCount();
        }, 0);
    }

    public function getMissingTranslationsCount(): int
    {
        return array_reduce($this->domainTranslations, function ($carry, $domainTranslation) {
            return $carry + $domainTranslation->getMissingTranslationsCount();
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
        foreach ($this->domainTranslations as $domainTranslation) {
            $data[$domainTranslation->getDomainName()] = $domainTranslation->toArray($withMetadata);
        }

        if ($withMetadata) {
            $data[self::METADATA_KEY_NAME] = [
                'count' => count($this->domainTranslations),
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
        foreach ($this->domainTranslations as $domainTranslation) {
            $domainTranslation->mergeTree($tree);
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
            list($count, $missing) = $this->updateCounters($subtree[$key]);

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
