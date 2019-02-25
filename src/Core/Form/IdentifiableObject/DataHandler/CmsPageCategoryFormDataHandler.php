<?php
/**
 * Created by PhpStorm.
 * User: tomas
 * Date: 19.2.25
 * Time: 10.00
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\AddCmsPageCategoryCommand;
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
        $cmsPageCategoryCommand = new AddCmsPageCategoryCommand(
            $data['name'],
            $data['friendly_url'],
            (int) $data['parent_category'],
            $data['is_displayed']
        );

        $cmsPageCategoryCommand
            ->setLocalisedDescription($data['description'])
            ->setLocalisedMetaDescription($data['meta_description'])
            ->setLocalisedMetaKeywords($data['meta_keywords'])
            ->setLocalisedMetaTitle($data['meta_title'])
            ->setShopAssociation(is_array($data['shop_association']) ? $data['shop_association'] : [])
        ;

        /** @var CmsPageCategoryId $result */
        $result = $this->commandBus->handle($cmsPageCategoryCommand);

        return $result->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        // TODO: Implement update() method.
    }
}
