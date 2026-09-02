<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\Page\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\BulkDeleteCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\CommandHandler\BulkDeleteCmsPageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CannotDeleteCmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShopException;

/**
 * Deletes multiple cms pages
 */
#[AsCommandHandler]
final class BulkDeleteCmsPageHandler extends AbstractCmsPageHandler implements BulkDeleteCmsPageHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageException
     */
    public function handle(BulkDeleteCmsPageCommand $command)
    {
        try {
            foreach ($command->getCmsPages() as $cmsPageId) {
                $cms = $this->getCmsPageIfExistsById($cmsPageId->getValue());

                if (false === $cms->delete()) {
                    throw new CannotDeleteCmsPageException(sprintf('An error occurred when deleting cms page with id %s', $cmsPageId->getValue()), CannotDeleteCmsPageException::FAILED_BULK_DELETE);
                }
            }
        } catch (PrestaShopException $exception) {
            throw new CmsPageException('An unexpected error occurred when deleting cms page', 0, $exception);
        }
    }
}
