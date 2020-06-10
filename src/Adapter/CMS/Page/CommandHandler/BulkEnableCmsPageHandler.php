<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\Page\CommandHandler;

use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\BulkEnableCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\CommandHandler\BulkEnableCmsPageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CannotEnableCmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageNotFoundException;
use PrestaShopException;

/**
 * Enables multiple cms pages.
 */
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
     * @throws \PrestaShopDatabaseException
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
