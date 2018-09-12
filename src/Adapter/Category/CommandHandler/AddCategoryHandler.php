<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Product\Category\CommandHandler\AddCategoryHandlerInterface;

/**
 * Class AddCategoryHandler
 *
 * @internal
 */
final class AddCategoryHandler implements AddCategoryHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function handle(AddCategoryCommand $command)
    {
        $category = new Category();
        $category->name = $command->getNames();
        $category->link_rewrite = $command->getLinkRewrites();
        $category->description = $command->getDescriptions();
        $category->id_parent = $command->getParentCategoryId();
        $category->meta_title = $command->getMetaTitles();
        $category->meta_description = $command->getMetaDescriptions();
        $category->meta_keywords = $command->getMetaKeywords();
        $category->groupBox = $command->getAssociatedGroupIds();

        $category->add();
    }
}
