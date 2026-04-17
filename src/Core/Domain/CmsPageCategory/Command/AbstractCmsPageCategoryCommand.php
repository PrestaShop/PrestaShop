<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command;

use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryConstraintException;

/**
 * Class AbstractCmsPageCategoryCommand
 */
abstract class AbstractCmsPageCategoryCommand
{
    public const CATEGORY_NAME_REGEX_PATTERN = '/^[^<>{}]*$/u';
    public const GENERIC_NAME_REGEX_PATTERN = '/^[^<>{}]*$/u';

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
