<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\Page\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\BulkDisableCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\CommandHandler\BulkDisableCmsPageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CannotDisableCmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageNotFoundException;
use PrestaShopDatabaseException;
use PrestaShopException;

/**
 * Disables multiple cms pages.
 */
#[AsCommandHandler]
final class BulkDisableCmsPageHandler extends AbstractCmsPageHandler implements BulkDisableCmsPageHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param BulkDisableCmsPageCommand $command
     *
     * @throws CannotDisableCmsPageException
     * @throws CmsPageException
     * @throws CmsPageNotFoundException
     */
    public function handle(BulkDisableCmsPageCommand $command)
    {
        try {
            $this->disableCmsPages($command);
        } catch (PrestaShopException $exception) {
            throw new CmsPageException('An error occurred when bulk disabling the cms pages', 0, $exception);
        }
    }

    /**
     * @param BulkDisableCmsPageCommand $command
     *
     * @throws CannotDisableCmsPageException
     * @throws PrestaShopException
     * @throws PrestaShopDatabaseException
     * @throws CmsPageException
     * @throws CmsPageNotFoundException
     */
    private function disableCmsPages(BulkDisableCmsPageCommand $command)
    {
        foreach ($command->getCmsPages() as $cmsPage) {
            $cms = $this->getCmsPageIfExistsById($cmsPage->getValue());

            $cms->active = false;

            if (false === $cms->update()) {
                throw new CannotDisableCmsPageException(sprintf('Failed to disable cms page with id %s', $cmsPage->getValue()));
            }
        }
    }
}
