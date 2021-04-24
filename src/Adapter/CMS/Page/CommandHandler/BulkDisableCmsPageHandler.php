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

use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\BulkDisableCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\CommandHandler\BulkDisableCmsPageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CannotDisableCmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageNotFoundException;
use PrestaShopException;

/**
 * Disables multiple cms pages.
 */
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
     * @throws \PrestaShopDatabaseException
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
