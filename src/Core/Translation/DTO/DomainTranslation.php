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

use Doctrine\Common\Inflector\Inflector;

/**
 * This class is a representation of a Domain catalogue.
 * It contains any message (wording) linked to it.
 * It gives methods to get the domain as an array or just the tree,
 * both with metadata including the number of messages and untranslated wordings
 */
class DomainTranslation
{
    /**
     * @var string
     */
    private $domainName;

    /**
     * @var MessageTranslation[]
     */
    private $messageTranslations;

    public function __construct(string $domainName)
    {
        $this->messageTranslations = [];
        $this->domainName = $domainName;
    }

    public function getDomainName(): string
    {
        return $this->domainName;
    }

    /**
     * @param MessageTranslation $messageTranslation
     */
    public function addMessageTranslation(MessageTranslation $messageTranslation): self
    {
        // if called twice with the same key, the second call will be ignored
        if (!array_key_exists($messageTranslation->getKey(), $this->messageTranslations)) {
            // The missing translations are placed on top
            if (!$messageTranslation->isTranslated()) {
                $this->messageTranslations = [$messageTranslation->getKey() => $messageTranslation] + $this->messageTranslations;
            } else {
                $this->messageTranslations[$messageTranslation->getKey()] = $messageTranslation;
            }
        }

        return $this;
    }

    /**
     * @return MessageTranslation[]
     */
    public function getMessageTranslations(): array
    {
        return $this->messageTranslations;
    }

    public function getTranslationsCount(): int
    {
        return count($this->messageTranslations);
    }

    public function getMissingTranslationsCount(): int
    {
        $missingTranslations = array_filter($this->messageTranslations, function (MessageTranslation $messagesTranslation) {
            return !$messagesTranslation->isTranslated();
        });

        return count($missingTranslations);
    }

    /**
     * Builds the domain metadata tree.
     *
     * Returns a structure like this:
     *
     * ```
     * [
     *     '__metadata' => [
     *         'count' => 11,
     *         'missing_translations' => 5
     *     ],
     *     'Admin' => [
     *         '__metadata' => [
     *             'count' => 11,
     *             'missing_translations' => 5
     *         ],
     *         'Foo' => [
     *             '__metadata' => [
     *                 'count' => 4,
     *                 'missing_translations' => 3
     *             ],
     *             'Bar' => [
     *                 '__metadata' => [
     *                     'count' => 2,
     *                     'missing_translations' => 1
     *                 ],
     *             ],
     *             'Baz' => [
     *                 '__metadata' => [
     *                     'count' => 2,
     *                     'missing_translations' => 2
     *                 ],
     *             ],
     *         ],
     *         'Plop' => [
     *             '__metadata' => [
     *                 'count' => 7,
     *                 'missing_translations' => 2
     *             ],
     *             'Foo' => [
     *                 '__metadata' => [
     *                     'count' => 2,
     *                     'missing_translations' => 0
     *                 ],
     *             ],
     *             'Bar' => [
     *                 '__metadata' => [
     *                     'count' => 3,
     *                     'missing_translations' => 1
     *                 ],
     *             ],
     *         ],
     *     ],
     * ];
     * ```
     *
     * @param array $tree
     *
     * @return array
     */
    public function mergeTree(array &$tree): array
    {
        if (empty($tree)) {
            $tree = [
                Translations::METADATA_KEY_NAME => Translations::EMPTY_META,
            ];
        }

        $parts = self::splitDomain($this->domainName);

        $content = $this->toArray();

        // start at the root
        $subtree = &$tree;
        $currentSubdomainName = '';

        foreach ($parts as $partNumber => $part) {
            $subdomainPartName = ucfirst($part);
            $currentSubdomainName .= $subdomainPartName;

            // create domain part branch if it doesn't exist
            if (!array_key_exists($subdomainPartName, $subtree)) {
                // only initialize tree leaves subtree with catalogue metadata
                // branches are initialized with empty metadata (which will be updated later)
                $isLastDomainPart = $partNumber === (count($parts) - 1);
                $subtree[$subdomainPartName][Translations::METADATA_KEY_NAME] = ($isLastDomainPart && isset($content[Translations::METADATA_KEY_NAME]))
                    ? $content[Translations::METADATA_KEY_NAME]
                    : Translations::EMPTY_META;
            }

            // move pointer to said branch
            $subtree = &$subtree[$subdomainPartName];
        }

        return $tree;
    }

    /**
     * Converts a domainName into Subdomains.
     * First, we split the camelcased name and add underscore between each part. For example DomainNameNumberOne will be Domain_Name_Number_One
     * Then, we explode the name in 3 parts based on _ separator. So Domain_Name_Number_One will be ['Domain', 'Name', 'Number_One']
     *
     * @param string $domain
     *
     * @return string[]
     */
    public static function splitDomain(string $domain): array
    {
        // the third component of the domain may have underscores, so we need to limit pieces to 3
        return explode('_', Inflector::tableize($domain), 3);
    }

    /**
     * @return array
     */
    public function toArray(bool $withMetadata = true): array
    {
        $data = [];
        foreach ($this->messageTranslations as $messageTranslation) {
            $messageData = $messageTranslation->toArray();
            $messageData['tree_domain'] = preg_split('/(?=[A-Z])/', $this->domainName, -1, PREG_SPLIT_NO_EMPTY);

            $data[$messageTranslation->getKey()] = $messageData;
        }

        if ($withMetadata) {
            $data[Translations::METADATA_KEY_NAME] = [
                'count' => count($this->messageTranslations),
                'missing_translations' => $this->getMissingTranslationsCount(),
            ];
        }

        return $data;
    }
}
