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

namespace PrestaShop\PrestaShop\Adapter\CMS\Page\CommandHandler;

use CMS;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\EditCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\CommandHandler\EditCmsPageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CannotEditCmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageNotFoundException;
use PrestaShopException;

/**
 * Edits cms page
 */
final class EditCmsPageHandler extends AbstractCmsPageHandler implements EditCmsPageHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageException
     */
    public function handle(EditCmsPageCommand $command)
    {
        $cms = $this->createCmsFromCommand($command);

        try {
            if (false === $cms->validateFields(false) || false === $cms->validateFieldsLang(false)) {
                throw new CmsPageException('Cms page contains invalid field values');
            }
            if (false === $cms->update()) {
                throw new CannotEditCmsPageException(
                    sprintf('Failed to update cms page with id %s', $command->getCmsPageId()->getValue())
                );
            }
            if (null !== $command->getShopAssociation()) {
                $this->associateWithShops($cms, $command->getShopAssociation());
            }
        } catch (PrestaShopException $e) {
            throw new CmsPageException(
                sprintf(
                    'An unexpected error occurred when editing cms page with id %s',
                    $command->getCmsPageId()->getValue()
                ),
                0,
                $e
            );
        }
    }

    /**
     * @param EditCmsPageCommand $command
     *
     * @return CMS
     *
     * @throws CmsPageException
     * @throws CmsPageNotFoundException
     */
    private function createCmsFromCommand(EditCmsPageCommand $command)
    {
        $cms = $this->getCmsPageIfExistsById($command->getCmsPageId()->getValue());

        if (null !== $command->getLocalizedTitle()) {
            $cms->meta_title = $command->getLocalizedTitle();
        }

        if (null !== $command->getLocalizedMetaTitle()) {
            $cms->head_seo_title = $command->getLocalizedMetaTitle();
        }

        if (null !== $command->getCmsPageCategoryId()) {
            $cms->id_cms_category = $command->getCmsPageCategoryId()->getValue();
        }

        if (null !== $command->getLocalizedMetaDescription()) {
            $cms->meta_description = $command->getLocalizedMetaDescription();
        }

        if (null !== $command->getLocalizedMetaKeyword()) {
            $cms->meta_keywords = $command->getLocalizedMetaKeyword();
        }

        if (null !== $command->getLocalizedFriendlyUrl()) {
            $cms->link_rewrite = $command->getLocalizedFriendlyUrl();
        }

        if (null !== $command->getLocalizedContent()) {
            $cms->content = $command->getLocalizedContent();
        }

        if (null !== $command->isIndexedForSearch()) {
            $cms->indexation = $command->isIndexedForSearch();
        }

        if (null !== $command->isDisplayed()) {
            $cms->active = $command->isDisplayed();
        }

        return $cms;
    }
}
