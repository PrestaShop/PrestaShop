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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\AddCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Command\EditCmsPageCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\Exception\CmsPageException;
use PrestaShop\PrestaShop\Core\Domain\CmsPage\ValueObject\CmsPageId;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;

/**
 * {@inheritdoc}
 */
final class CmsPageFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(CommandBusInterface $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * Create object from form data.
     *
     * @param array $data
     *
     * @return int
     *
     * @throws CmsPageCategoryException
     */
    public function create(array $data)
    {
        /**
         * @var CmsPageId
         */
        $cmsPageId = $this->commandBus->handle(new AddCmsPageCommand(
            (int) $data['page_category_id'],
            $data['title'],
            $data['meta_title'],
            $data['meta_description'],
            $data['meta_keyword'],
            $data['friendly_url'],
            $data['content'],
            $data['is_indexed_for_search'],
            $data['is_displayed'],
            is_array($data['shop_association']) ? $data['shop_association'] : []
        ));

        return $cmsPageId->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @throws CmsPageException
     * @throws CmsPageCategoryException
     */
    public function update($cmsPageId, array $data)
    {
        $editCmsPageCommand = new EditCmsPageCommand((int) $cmsPageId);
        $this->fillCommandWithData($editCmsPageCommand, $data);

        $this->commandBus->handle($editCmsPageCommand);
    }

    /**
     * @param EditCmsPageCommand $command
     * @param array $data
     *
     * @throws CmsPageCategoryException
     */
    private function fillCommandWithData(EditCmsPageCommand $command, array $data)
    {
        $command->setCmsPageCategoryId((int) $data['page_category_id']);
        $command->setLocalizedTitle($data['title']);
        $command->setLocalizedMetaTitle($data['meta_title']);
        $command->setLocalizedMetaDescription($data['meta_description']);
        $command->setLocalizedMetaKeyword($data['meta_keyword']);
        $command->setLocalizedFriendlyUrl($data['friendly_url']);
        $command->setLocalizedContent($data['content']);
        $command->setIsIndexedForSearch($data['is_indexed_for_search']);
        $command->setIsDisplayed($data['is_displayed']);
        $command->setShopAssociation($data['shop_association']);
    }
}
