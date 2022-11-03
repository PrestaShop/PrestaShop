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
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\AddCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\EditCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\ValueObject\CmsPageCategoryId;

/**
 * Class CmsPageCategoryFormDataHandler is responsible for creating and updating cms page category form data.
 */
final class CmsPageCategoryFormDataHandler implements FormDataHandlerInterface
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
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function create(array $data)
    {
        $addCmsPageCategoryCommand = new AddCmsPageCategoryCommand(
            $data['name'],
            $data['friendly_url'],
            (int) $data['parent_category'],
            $data['is_displayed']
        );

        $addCmsPageCategoryCommand
            ->setLocalisedDescription($data['description'])
            ->setLocalisedMetaDescription($data['meta_description'])
            ->setLocalisedMetaKeywords($data['meta_keywords'])
            ->setLocalisedMetaTitle($data['meta_title'])
            ->setShopAssociation(is_array($data['shop_association']) ? $data['shop_association'] : [])
        ;

        /** @var CmsPageCategoryId $result */
        $result = $this->commandBus->handle($addCmsPageCategoryCommand);

        return $result->getValue();
    }

    /**
     * {@inheritdoc}
     *
     * @throws CmsPageCategoryException
     */
    public function update($id, array $data)
    {
        $editCmsPageCategoryCommand = new EditCmsPageCategoryCommand((int) $id);

        $editCmsPageCategoryCommand
            ->setLocalisedName($data['name'])
            ->setLocalisedFriendlyUrl($data['friendly_url'])
            ->setParentId((int) $data['parent_category'])
            ->setIsDisplayed($data['is_displayed'])
            ->setLocalisedDescription($data['description'])
            ->setLocalisedMetaDescription($data['meta_description'])
            ->setLocalisedMetaKeywords($data['meta_keywords'])
            ->setLocalisedMetaTitle($data['meta_title'])
            ->setShopAssociation(is_array($data['shop_association']) ? $data['shop_association'] : [])
        ;

        $this->commandBus->handle($editCmsPageCategoryCommand);
    }
}
