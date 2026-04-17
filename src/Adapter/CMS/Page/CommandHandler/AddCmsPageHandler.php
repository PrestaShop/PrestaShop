<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        $cms->link_rewrite = $command->getLocalizedFriendlyUrl();
        $cms->content = $command->getLocalizedContent();
        $cms->indexation = $command->isIndexedForSearch();
        $cms->active = $command->isDisplayed();

        return $cms;
    }
}
