<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Validation\RegexPattern;

/**
 * Class AbstractCmsPageCategoryCommand
 */
abstract class AbstractCmsPageCategoryCommand
{
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
            if (!preg_match(RegexPattern::CATALOG_NAME, $name)) {
                throw new CmsPageCategoryConstraintException(
                    sprintf(
                      'Given category name "%s" does not match pattern "%s"',
                      $name,
                      RegexPattern::CATALOG_NAME
                    ),
                    CmsPageCategoryConstraintException::INVALID_CATEGORY_NAME
                );
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
            throw new CmsPageCategoryConstraintException(
                sprintf(
                    'Given meta title "%s" does not match pattern "%s"',
                    $assertionResult,
                    RegexPattern::GENERIC_NAME
                ),
                CmsPageCategoryConstraintException::INVALID_META_TITLE
            );
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
            throw new CmsPageCategoryConstraintException(
                sprintf(
                    'Given meta description "%s" does not match pattern "%s"',
                    $assertionResult,
                    RegexPattern::GENERIC_NAME
                ),
                CmsPageCategoryConstraintException::INVALID_META_DESCRIPTION
            );
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
            throw new CmsPageCategoryConstraintException(
                sprintf(
                    'Given meta keyword "%s" does not match pattern "%s"',
                    $assertionResult,
                    RegexPattern::GENERIC_NAME
                ),
                CmsPageCategoryConstraintException::INVALID_META_KEYWORDS
            );
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
            if (!preg_match(RegexPattern::GENERIC_NAME, $localisedName)) {
                return $localisedName;
            }
        }

        return true;
    }
}
