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

namespace PrestaShop\PrestaShop\Adapter\Util\Entity;

use PrestaShop\PrestaShop\Core\Util\String\StringModifierInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class EntityNameDuplicator
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var StringModifierInterface
     */
    private $stringModifier;

    /**
     * @var array
     */
    protected $languages;

    /**
     * @var array
     */
    protected $locales;

    /**
     * @param TranslatorInterface $translator
     * @param StringModifierInterface $stringModifier
     * @param array $languages
     */
    public function __construct(
        TranslatorInterface $translator,
        StringModifierInterface $stringModifier,
        array $languages
    ) {
        $this->translator = $translator;
        $this->stringModifier = $stringModifier;
        $this->languages = $languages;
    }

    /**
     * Adds a "copy" word to localized entity names
     *
     * @param array<int, string> $currentLocalizedNames Array with localized names, IdLang => Name
     * @param int $maxLength max length of a new name, will be cut if it doesn't fit with the new "copy" word
     *
     * @return array<int, string>
     */
    public function getNewLocalizedNames(array $currentLocalizedNames, int $maxLength): array
    {
        if (empty($this->locales)) {
            $this->buildLocales();
        }

        $newLocalizedNames = [];
        foreach ($currentLocalizedNames as $langId => $currentName) {
            $langId = (int) $langId;

            // Add copy word to the string
            $namePattern = $this->translator->trans('copy of %s', [], 'Admin.Catalog.Feature', $this->locales[$langId]);
            $newName = sprintf($namePattern, $currentName);

            // Save and cut the end if it doesn't fit to the limit
            $newLocalizedNames[$langId] = $this->stringModifier->cutEnd($newName, $maxLength);
        }

        return $newLocalizedNames;
    }

    /**
     * Appends a "copy" to simple entity name
     *
     * @param string $currentName Current name of the entity
     * @param int $maxLength max length of a new name, will be cut if it doesn't fit with the new "copy" word
     * @param int $langId
     *
     * @return string
     */
    public function getNewName(string $currentName, int $maxLength, int $langId): string
    {
        if (empty($this->locales)) {
            $this->buildLocales();
        }

        // Add copy word to the string
        $namePattern = $this->translator->trans('copy of %s', [], 'Admin.Catalog.Feature', $this->locales[$langId]);
        $newName = sprintf($namePattern, $currentName);

        // Cut the end if it doesn't fit to the limit and return
        return $this->stringModifier->cutEnd($newName, $maxLength);
    }

    /**
     * Builds the locale array
     *
     * @return void
     */
    private function buildLocales(): void
    {
        foreach ($this->languages as $language) {
            $this->locales[(int) $language['id_lang']] = $language['locale'];
        }
    }
}
