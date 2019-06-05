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

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\CommandHandler;

use CMSCategory;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\AddCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CommandHandler\AddCmsPageCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CannotAddCmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;
use PrestaShopException;

/**
 * Adds cms page category
 */
final class AddCmsPageCategoryHandler extends AbstractCmsPageCategoryHandler implements AddCmsPageCategoryHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function handle(AddCmsPageCategoryCommand $command)
    {
        if (!$this->assertHasDefaultLanguage($command->getLocalisedName())) {
            throw new CmsPageCategoryConstraintException(
                'Missing name in default language',
                CmsPageCategoryConstraintException::MISSING_DEFAULT_LANGUAGE_FOR_NAME
            );
        }

        if (!$this->assertHasDefaultLanguage($command->getLocalisedFriendlyUrl())) {
            throw new CmsPageCategoryConstraintException(
                'Missing friendly url in default language',
                CmsPageCategoryConstraintException::MISSING_DEFAULT_LANGUAGE_FOR_FRIENDLY_URL
            );
        }

        $this->assertIsValidLinkRewrite($command->getLocalisedFriendlyUrl());
        $this->assertDescriptionContainsCleanHtml($command->getLocalisedDescription());

        try {
            $cmsPageCategory = new CMSCategory();
            $cmsPageCategory->name = $command->getLocalisedName();
            $cmsPageCategory->active = $command->isDisplayed();
            $cmsPageCategory->id_parent = $command->getParentId()->getValue();
            $cmsPageCategory->description = $command->getLocalisedDescription();
            $cmsPageCategory->meta_title = $command->getLocalisedMetaTitle();
            $cmsPageCategory->meta_description = $command->getLocalisedMetaDescription();
            $cmsPageCategory->meta_keywords = $command->getLocalisedMetaKeywords();

            $cmsPageCategory->link_rewrite = $command->getLocalisedFriendlyUrl();

            if (false === $cmsPageCategory->add()) {
                throw new CannotAddCmsPageCategoryException(
                    'Failed to add cms page category'
                );
            }

            $this->associateWithShops($cmsPageCategory, $command->getShopAssociation());
        } catch (PrestaShopException $exception) {
            throw new CmsPageCategoryException(
                'An unexpected error occurred when adding cms page category',
                0,
                $exception
            );
        }

        return new CmsPageCategoryId((int) $cmsPageCategory->id);
    }
}
