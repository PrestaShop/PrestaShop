<?php
/**
 * 2007-2019 PrestaShop and Contributors
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
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\CMS\PageCategory\CommandHandler;

use CMSCategory;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\ToggleCmsPageCategoryStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\CommandHandler\ToggleCmsPageCategoryStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CannotToggleCmsPageCategoryStatusException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryNotFoundException;
use PrestaShopException;

/**
 * Class ToggleCmsPageCategoryStatusHandler is responsible for turning on and off cms page category status.
 */
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
                throw new CmsPageCategoryNotFoundException(
                    sprintf(
                        'Cms category object with id "%s" has not been found for status changing.',
                        $command->getCmsPageCategoryId()->getValue()
                    )
                );
            }

            if (false === $entity->toggleStatus()) {
                throw new CannotToggleCmsPageCategoryStatusException(
                    sprintf(
                        'Unable to toggle cms category with id "%s"',
                        $command->getCmsPageCategoryId()->getValue()
                    )
                );
            }
        } catch (PrestaShopException $exception) {
            throw new CmsPageCategoryException(
                sprintf(
                    'An error occurred when toggling status for cms page object with id "%s"',
                    $command->getCmsPageCategoryId()->getValue()
                ),
                0,
                $exception
            );
        }
    }
}
