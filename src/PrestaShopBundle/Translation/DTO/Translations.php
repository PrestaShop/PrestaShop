<?php

/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
declare(strict_types=1);

namespace PrestaShopBundle\Translation\DTO;

class Translations
{
    /**
     * @var DomainTranslation[]
     */
    private $domainTranslations;

    public function __construct()
    {
        $this->domainTranslations = [];
    }

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

    /**
     * @return DomainTranslation[]
     */
    public function getDomainTranslations(): array
    {
        return $this->domainTranslations;
    }

    public function getMissingTranslationsCount(): int
    {
        $missingTranslations = 0;
        foreach ($this->domainTranslations as $domainTranslation) {
            $missingTranslations += $domainTranslation->getMissingTranslationsCount();
        }

        return $missingTranslations;
    }

    /**
     * @return array
     */
    public function toArray(bool $withMetadata = true): array
    {
        $data = [];
        foreach ($this->domainTranslations as $domainTranslation) {
            $data[$domainTranslation->getDomainName()] = $domainTranslation->toArray($withMetadata);
        }

        if ($withMetadata) {
            $data['__metadata'] = [
                'count' => count($this->domainTranslations),
                'missing_translations' => $this->getMissingTranslationsCount(),
            ];
        }

        ksort($data);

        return $data;
    }

    public function getTree()
    {
        // template for initializing metadata
        $emptyMeta = [
            'count' => 0,
            'missing_translations' => 0,
        ];

        $tree = [
            '__metadata' => $emptyMeta,
        ];
        foreach ($this->domainTranslations as $domainTranslation) {
            $domainTranslation->getTree($tree);
        }

        $this->updateCounters($tree);

        return $tree;
    }

    /**
     * @TODO This method will be added to TreeBuilder
     *
     * Updates counters of this subtree by adding the sum of children's counters
     *
     * @param array $subtree
     *
     * @return array Array of [sum of count, sum of missing_translations]
     */
    private function updateCounters(array &$subtree): array
    {
        foreach ($subtree as $key => $values) {
            if ($key === '__metadata') {
                continue;
            }

            // update child and get its counters
            list($count, $missing) = $this->updateCounters($subtree[$key]);

            // update this tree's counters by adding the child's
            $subtree['__metadata']['count'] += $count;
            $subtree['__metadata']['missing_translations'] += $missing;
        }

        return [
            $subtree['__metadata']['count'],
            $subtree['__metadata']['missing_translations'],
        ];
    }
}
