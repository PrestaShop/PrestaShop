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

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\CommandHandler;

use CMSCategory;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\BulkEnableCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CommandHandler\BulkEnableCmsPageCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CannotEnableCmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryNotFoundException;
use PrestaShopException;

/**
 * Class BulkEnableCmsPageCategoryCommand is responsible for enabling cms category pages.
 */
final class BulkEnableCmsPageCategoryHandler implements BulkEnableCmsPageCategoryHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function handle(BulkEnableCmsPageCategoryCommand $command)
    {
        try {
            foreach ($command->getCmsPageCategoryIds() as $cmsPageCategoryId) {
                $entity = new CMSCategory($cmsPageCategoryId->getValue());

                if (0 >= $entity->id) {
                    throw new CmsPageCategoryNotFoundException(sprintf('Cms category object with id "%s" has not been found for enabling status.', $cmsPageCategoryId->getValue()));
                }

                $entity->active = true;

                if (false === $entity->update()) {
                    throw new CannotEnableCmsPageCategoryException(sprintf('Unable to enable cms category object with id "%s"', $cmsPageCategoryId->getValue()));
                }
            }
        } catch (PrestaShopException $e) {
            throw new CmsPageCategoryException('Unexpected error occurred when handling bulk enable cms category', 0, $e);
        }
    }
}
