<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\Page\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\ToggleCmsPageStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\CommandHandler\ToggleCmsPageStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CannotToggleCmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShopException;

/**
 * Changes the status of cms page.
 */
#[AsCommandHandler]
final class ToggleCmsPageStatusHandler extends AbstractCmsPageHandler implements ToggleCmsPageStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageException
     */
    public function handle(ToggleCmsPageStatusCommand $command)
    {
        $cms = $this->getCmsPageIfExistsById($command->getCmsPageId()->getValue());

        try {
            if (false === $cms->toggleStatus()) {
                throw new CannotToggleCmsPageException(sprintf('Failed to toggle cms page with id %s status', $command->getCmsPageId()->getValue()));
            }
        } catch (PrestaShopException) {
            throw new CmsPageException(sprintf('An unexpected error occurred when toggling cms page with id %s status', $command->getCmsPageId()->getValue()));
        }
    }
}
