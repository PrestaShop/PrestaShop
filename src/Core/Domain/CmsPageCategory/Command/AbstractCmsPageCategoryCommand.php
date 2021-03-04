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

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryConstraintException;

/**
 * Class AbstractCmsPageCategoryCommand
 */
abstract class AbstractCmsPageCategoryCommand
{
    public const CATEGORY_NAME_REGEX_PATTERN = '/^[^<>;=#{}]*$/u';
    public const GENERIC_NAME_REGEX_PATTERN = '/^[^<>={}]*$/u';

    /**
     * Checks if given names matches pattern.
     *
     * @param array $names
     *
     * @throws CmsPageCategoryConstraintException
     */
    protected function assertCategoryName(array $names)
    {
        foreach ($names as $name) {
            if (!preg_match(self::CATEGORY_NAME_REGEX_PATTERN, $name)) {
                throw new CmsPageCategoryConstraintException(sprintf('Given category name "%s" does not match pattern "%s"', $name, self::CATEGORY_NAME_REGEX_PATTERN), CmsPageCategoryConstraintException::INVALID_CATEGORY_NAME);
            }
        }
    }

    /**
     * @param array $localisedMetaTitles
     *
     * @throws CmsPageCategoryConstraintException
     */
    protected function assertIsGenericNameForMetaTitle(array $localisedMetaTitles)
    {
        $assertionResult = $this->assertIsGenericName($localisedMetaTitles);

        if (true !== $assertionResult) {
            throw new CmsPageCategoryConstraintException(sprintf('Given meta title "%s" does not match pattern "%s"', $assertionResult, self::GENERIC_NAME_REGEX_PATTERN), CmsPageCategoryConstraintException::INVALID_META_TITLE);
        }
    }

    /**
     * @param array $localisedMetaDescription
     *
     * @throws CmsPageCategoryConstraintException
     */
    protected function assertIsGenericNameForMetaDescription(array $localisedMetaDescription)
    {
        $assertionResult = $this->assertIsGenericName($localisedMetaDescription);

        if (true !== $assertionResult) {
            throw new CmsPageCategoryConstraintException(sprintf('Given meta description "%s" does not match pattern "%s"', $assertionResult, self::GENERIC_NAME_REGEX_PATTERN), CmsPageCategoryConstraintException::INVALID_META_DESCRIPTION);
        }
    }

    /**
     * @param array $localisedMetaKeywords
     *
     * @throws CmsPageCategoryConstraintException
     */
    protected function assertIsGenericNameForMetaKeywords(array $localisedMetaKeywords)
    {
        $assertionResult = $this->assertIsGenericName($localisedMetaKeywords);

        if (true !== $assertionResult) {
            throw new CmsPageCategoryConstraintException(sprintf('Given meta keyword "%s" does not match pattern "%s"', $assertionResult, self::GENERIC_NAME_REGEX_PATTERN), CmsPageCategoryConstraintException::INVALID_META_KEYWORDS);
        }
    }

    /**
     * @param array $localisedNames
     *
     * @return bool|string
     */
    private function assertIsGenericName(array $localisedNames)
    {
        foreach ($localisedNames as $localisedName) {
            if (!preg_match(self::GENERIC_NAME_REGEX_PATTERN, $localisedName)) {
                return $localisedName;
            }
        }

        return true;
    }
}
