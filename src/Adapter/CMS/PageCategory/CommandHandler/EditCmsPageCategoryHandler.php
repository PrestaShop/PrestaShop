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

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\CommandHandler;

use CMSCategory;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\EditCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CommandHandler\EditCmsPageCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CannotUpdateCmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryNotFoundException;
use PrestaShopException;

/**
 * Edits cms page category.
 */
#[AsCommandHandler]
final class EditCmsPageCategoryHandler extends AbstractCmsPageCategoryHandler implements EditCmsPageCategoryHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function handle(EditCmsPageCategoryCommand $command)
    {
        try {
            $cmsPageCategory = new CMSCategory($command->getCmsPageCategoryId()->getValue());

            if (0 >= $cmsPageCategory->id) {
                throw new CmsPageCategoryNotFoundException(sprintf('Unable to find cms page category with id "%s"', $cmsPageCategory->id));
            }

            if (null !== $command->getLocalisedName()) {
                if (!$this->assertHasDefaultLanguage($command->getLocalisedName())) {
                    throw new CmsPageCategoryConstraintException('Missing name in default language', CmsPageCategoryConstraintException::MISSING_DEFAULT_LANGUAGE_FOR_NAME);
                }
                $cmsPageCategory->name = $command->getLocalisedName();
            }

            if (null !== $command->isDisplayed()) {
                $cmsPageCategory->active = $command->isDisplayed();
            }

            if (null !== $command->getParentId()) {
                $this->assertCmsCategoryCanBeMovedToParent(
                    $command->getCmsPageCategoryId()->getValue(),
                    $command->getParentId()->getValue()
                );
                $cmsPageCategory->id_parent = $command->getParentId()->getValue();
            }

            if (null !== $command->getLocalisedDescription()) {
                $this->assertDescriptionContainsCleanHtml($command->getLocalisedDescription());
                $cmsPageCategory->description = $command->getLocalisedDescription();
            }

            if (null !== $command->getLocalisedMetaTitle()) {
                $cmsPageCategory->meta_title = $command->getLocalisedMetaTitle();
            }

            if (null !== $command->getLocalisedMetaDescription()) {
                $cmsPageCategory->meta_description = $command->getLocalisedMetaDescription();
            }

            if (null !== $command->getLocalisedFriendlyUrl()) {
                if (!$this->assertHasDefaultLanguage($command->getLocalisedFriendlyUrl())) {
                    throw new CmsPageCategoryConstraintException('Missing friendly url in default language', CmsPageCategoryConstraintException::MISSING_DEFAULT_LANGUAGE_FOR_FRIENDLY_URL);
                }
                $this->assertIsValidLinkRewrite($command->getLocalisedFriendlyUrl());

                $cmsPageCategory->link_rewrite = $command->getLocalisedFriendlyUrl();
            }

            if (false === $cmsPageCategory->update()) {
                throw new CannotUpdateCmsPageCategoryException('Failed to update cms page category');
            }

            if (null !== $command->getShopAssociation()) {
                $this->associateWithShops($cmsPageCategory, $command->getShopAssociation());
            }
        } catch (PrestaShopException $exception) {
            throw new CmsPageCategoryException('An unexpected error occurred when updating cms page category', 0, $exception);
        }
    }

    /**
     * Adds if the current category is not being moved to the same category or its own child.
     *
     * @param int $cmsCategoryId
     * @param int $cmsCategoryParentId
     *
     * @throws CmsPageCategoryConstraintException
     */
    private function assertCmsCategoryCanBeMovedToParent($cmsCategoryId, $cmsCategoryParentId)
    {
        if (!CMSCategory::checkBeforeMove($cmsCategoryId, $cmsCategoryParentId)) {
            throw new CmsPageCategoryConstraintException(sprintf('Unable to move cms category "%s" to parent category "%s"', $cmsCategoryId, $cmsCategoryParentId), CmsPageCategoryConstraintException::CANNOT_MOVE_CATEGORY_TO_PARENT);
        }
    }
}
