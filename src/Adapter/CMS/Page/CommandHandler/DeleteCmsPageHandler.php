<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\Page\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\DeleteCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\CommandHandler\DeleteCmsPageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CannotDeleteCmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShopException;

/**
 * Deletes given cms page.
 */
#[AsCommandHandler]
final class DeleteCmsPageHandler extends AbstractCmsPageHandler implements DeleteCmsPageHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageException
     */
    public function handle(DeleteCmsPageCommand $command)
    {
        $cms = $this->getCmsPageIfExistsById($command->getCmsPageId()->getValue());

        try {
            if (false === $cms->delete()) {
                throw new CannotDeleteCmsPageException(sprintf('An error occurred when deleting cms page with id %s', $command->getCmsPageId()->getValue()), CannotDeleteCmsPageException::FAILED_DELETE);
            }
        } catch (PrestaShopException $e) {
            throw new CmsPageException(sprintf('An unexpected error occurred when deleting cms page with id %s', $command->getCmsPageId()->getValue()), 0, $e);
        }
    }
}
