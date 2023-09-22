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

namespace PrestaShop\PrestaShop\Adapter\CMS\Page\CommandHandler;

use CMS;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\AddCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\CommandHandler\AddCmsPageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CannotAddCmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\ValueObject\CmsPageId;
use PrestaShopException;

/**
 * Handles AddCmsPageCommand using legacy object model
 */
#[AsCommandHandler]
final class AddCmsPageHandler extends AbstractCmsPageHandler implements AddCmsPageHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddCmsPageCommand $command)
    {
        $cms = $this->createCmsFromCommand($command);

        try {
            if (false === $cms->validateFields(false) || false === $cms->validateFieldsLang(false)) {
                throw new CmsPageException('Cms page contains invalid field values');
            }

            if (false === $cms->add()) {
                throw new CannotAddCmsPageException('Failed to add cms page');
            }
            $this->associateWithShops($cms, $command->getShopAssociation());
        } catch (PrestaShopException $e) {
            throw new CmsPageException('An unexpected error occurred when adding cms page', 0, $e);
        }

        return new CmsPageId((int) $cms->id);
    }

    /**
     * @param AddCmsPageCommand $command
     *
     * @return CMS
     */
    protected function createCmsFromCommand(AddCmsPageCommand $command)
    {
        $cmsCategoryId = $command->getCmsPageCategory()->getValue();
        $this->assertCmsCategoryExists($cmsCategoryId);

        $cms = new CMS();
        $cms->id_cms_category = $cmsCategoryId;
        $cms->meta_title = $command->getLocalizedTitle();
        $cms->head_seo_title = $command->getLocalizedMetaTitle();
        $cms->meta_description = $command->getLocalizedMetaDescription();
        $cms->meta_keywords = $command->getLocalizedMetaKeyword();
        $cms->link_rewrite = $command->getLocalizedFriendlyUrl();
        $cms->content = $command->getLocalizedContent();
        $cms->indexation = $command->isIndexedForSearch();
        $cms->active = $command->isDisplayed();

        return $cms;
    }
}
