<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\Page\CommandHandler;

use CMS;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\EditCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\CommandHandler\EditCmsPageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CannotEditCmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShopException;

/**
 * Edits cms page
 */
#[AsCommandHandler]
final class EditCmsPageHandler extends AbstractCmsPageHandler implements EditCmsPageHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageException
     * @throws CmsPageCategoryException
     */
    public function handle(EditCmsPageCommand $command)
    {
        $cms = $this->createCmsFromCommand($command);

        try {
            if (false === $cms->validateFields(false) || false === $cms->validateFieldsLang(false)) {
                throw new CmsPageException('Cms page contains invalid field values');
            }
            if (false === $cms->update()) {
                throw new CannotEditCmsPageException(sprintf('Failed to update cms page with id %s', $command->getCmsPageId()->getValue()));
            }
            if (null !== $command->getShopAssociation()) {
                $this->associateWithShops($cms, $command->getShopAssociation());
            }
        } catch (PrestaShopException $e) {
            throw new CmsPageException(sprintf('An unexpected error occurred when editing cms page with id %s', $command->getCmsPageId()->getValue()), 0, $e);
        }
    }

    /**
     * @param EditCmsPageCommand $command
     *
     * @return CMS
     *
     * @throws CmsPageException
     * @throws CmsPageNotFoundException
     * @throws CmsPageCategoryException
     */
    private function createCmsFromCommand(EditCmsPageCommand $command)
    {
        $cms = $this->getCmsPageIfExistsById($command->getCmsPageId()->getValue());

        if (null !== $command->getCmsPageCategoryId()) {
            $this->assertCmsCategoryExists($command->getCmsPageCategoryId()->getValue());

            $cms->id_cms_category = $command->getCmsPageCategoryId()->getValue();
        }

        if (null !== $command->getLocalizedTitle()) {
            $cms->meta_title = $command->getLocalizedTitle();
        }

        if (null !== $command->getLocalizedMetaTitle()) {
            $cms->head_seo_title = $command->getLocalizedMetaTitle();
        }

        if (null !== $command->getLocalizedMetaDescription()) {
            $cms->meta_description = $command->getLocalizedMetaDescription();
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
