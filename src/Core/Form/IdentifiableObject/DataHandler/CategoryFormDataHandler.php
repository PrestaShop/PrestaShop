<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\RedirectOption;

/**
 * Creates/updates category from data submitted in category form
 *
 * @internal
 */
final class CategoryFormDataHandler implements FormDataHandlerInterface
{
    /**
     * @var CommandBusInterface
     */
    private $commandBus;

    /**
     * @param CommandBusInterface $commandBus
     */
    public function __construct(
        CommandBusInterface $commandBus
    ) {
        $this->commandBus = $commandBus;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        $command = $this->createAddCategoryCommand($data);

        /** @var CategoryId $categoryId */
        $categoryId = $this->commandBus->handle($command);

        return $categoryId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($categoryId, array $data)
    {
        $command = $this->createEditCategoryCommand((int) $categoryId, $data);

        $this->commandBus->handle($command);
    }

    /**
     * Creates add category command from form data
     *
     * @param array $data
     *
     * @return AddCategoryCommand
     *
     * @throws CategoryConstraintException
     */
    private function createAddCategoryCommand(array $data): AddCategoryCommand
    {
        $command = new AddCategoryCommand(
            $data['name'],
            $data['link_rewrite'],
            (bool) $data['active'],
            (int) $data['id_parent']
        );

        $command->setLocalizedDescriptions($data['description']);
        $command->setLocalizedAdditionalDescriptions($data['additional_description']);
        $command->setLocalizedMetaTitles($data['meta_title']);
        $command->setLocalizedMetaDescriptions($data['meta_description']);
        $command->setAssociatedGroupIds($data['group_association']);
        $command->setCoverImage($data['cover_image']);
        $command->setThumbnailImage($data['thumbnail_image']);
        if (isset($data['shop_association'])) {
            $command->setAssociatedShopIds($data['shop_association']);
        }

        $redirectOption = new RedirectOption(
            $data['redirect_option']['type'],
            $data['redirect_option']['target']['id'] ?? 0
        );

        $command->setRedirectOption($redirectOption);

        return $command;
    }

    /**
     * Creates edit category command from
     *
     * @param int $categoryId
     * @param array $data
     *
     * @return EditCategoryCommand
     *
     * @throws CategoryConstraintException
     */
    private function createEditCategoryCommand(int $categoryId, array $data): EditCategoryCommand
    {
        $command = new EditCategoryCommand($categoryId);
        $command->setIsActive($data['active']);
        $command->setLocalizedLinkRewrites($data['link_rewrite']);
        $command->setLocalizedNames($data['name']);
        $command->setParentCategoryId($data['id_parent']);
        $command->setLocalizedDescriptions($data['description']);
        $command->setLocalizedAdditionalDescriptions($data['additional_description']);
        $command->setLocalizedMetaTitles($data['meta_title']);
        $command->setLocalizedMetaDescriptions($data['meta_description']);
        $command->setAssociatedGroupIds($data['group_association']);
        $command->setCoverImage($data['cover_image']);
        $command->setThumbnailImage($data['thumbnail_image']);
        if (isset($data['shop_association'])) {
            $command->setAssociatedShopIds($data['shop_association']);
        }

        $redirectOption = new RedirectOption(
            $data['redirect_option']['type'],
            $data['redirect_option']['target']['id'] ?? 0
        );

        $command->setRedirectOption($redirectOption);

        return $command;
    }
}
