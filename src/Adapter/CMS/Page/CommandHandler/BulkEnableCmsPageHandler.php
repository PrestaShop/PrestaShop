<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\Page\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\BulkEnableCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\CommandHandler\BulkEnableCmsPageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CannotEnableCmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageNotFoundException;
use PrestaShopDatabaseException;
use PrestaShopException;

/**
 * Enables multiple cms pages.
 */
#[AsCommandHandler]
final class BulkEnableCmsPageHandler extends AbstractCmsPageHandler implements BulkEnableCmsPageHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageException
     */
    public function handle(BulkEnableCmsPageCommand $command)
    {
        try {
            $this->enableCmsPages($command);
        } catch (PrestaShopException $exception) {
            throw new CmsPageException('An error occurred when bulk enabling the cms pages', 0, $exception);
        }
    }

    /**
     * @param BulkEnableCmsPageCommand $command
     *
     * @throws CannotEnableCmsPageException
     * @throws CmsPageException
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     * @throws CmsPageNotFoundException
     */
    private function enableCmsPages(BulkEnableCmsPageCommand $command)
    {
        foreach ($command->getCmsPages() as $cmsPage) {
            $cms = $this->getCmsPageIfExistsById($cmsPage->getValue());

            $cms->active = true;

            if (false === $cms->update()) {
                throw new CannotEnableCmsPageException(sprintf('Failed to enable cms page with id %s', $cmsPage->getValue()));
            }
        }
    }
}
