<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\CommandHandler;

use CMSCategory;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\ToggleCmsPageCategoryStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CommandHandler\ToggleCmsPageCategoryStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CannotToggleCmsPageCategoryStatusException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryNotFoundException;
use PrestaShopException;

/**
 * Class ToggleCmsPageCategoryStatusHandler is responsible for turning on and off cms page category status.
 */
#[AsCommandHandler]
final class ToggleCmsPageCategoryStatusHandler implements ToggleCmsPageCategoryStatusHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function handle(ToggleCmsPageCategoryStatusCommand $command)
    {
        try {
            $entity = new CMSCategory($command->getCmsPageCategoryId()->getValue());

            if (0 >= $entity->id) {
                throw new CmsPageCategoryNotFoundException(sprintf('Cms category object with id "%s" has not been found for status changing.', $command->getCmsPageCategoryId()->getValue()));
            }

            if (false === $entity->toggleStatus()) {
                throw new CannotToggleCmsPageCategoryStatusException(sprintf('Unable to toggle cms category with id "%s"', $command->getCmsPageCategoryId()->getValue()));
            }
        } catch (PrestaShopException $exception) {
            throw new CmsPageCategoryException(sprintf('An error occurred when toggling status for cms page object with id "%s"', $command->getCmsPageCategoryId()->getValue()), 0, $exception);
        }
    }
}
