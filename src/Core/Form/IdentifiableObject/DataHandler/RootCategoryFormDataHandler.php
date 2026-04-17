<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\DataHandler;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\EditRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\RedirectOption;

/**
 * Creates/updates root category from data submitted in category form
 *
 * @internal
 */
final class RootCategoryFormDataHandler implements FormDataHandlerInterface
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
        $command = $this->createAddRootCategoryCommand($data);

        /** @var CategoryId $categoryId */
        $categoryId = $this->commandBus->handle($command);

        return $categoryId->getValue();
    }

    /**
     * {@inheritdoc}
     */
    public function update($categoryId, array $data)
    {
        $command = $this->createEditRootCategoryCommand((int) $categoryId, $data);

        $this->commandBus->handle($command);
    }

    /**
     * Creates command with form data for adding new root category
     *
     * @param array $data
     *
     * @return AddRootCategoryCommand
     *
     * @throws CategoryConstraintException
     */
    public function createAddRootCategoryCommand(array $data): AddRootCategoryCommand
    {
        $command = new AddRootCategoryCommand(
            $data['name'],
            $data['link_rewrite'],
            $data['active']
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
     * @param int $rootCategoryId
     * @param array $data
     *
     * @return EditRootCategoryCommand
     *
     * @throws CategoryConstraintException
     */
    private function createEditRootCategoryCommand(int $rootCategoryId, array $data): EditRootCategoryCommand
    {
        $command = new EditRootCategoryCommand($rootCategoryId);
        $command->setIsActive($data['active']);
        $command->setLocalizedLinkRewrites($data['link_rewrite']);
        $command->setLocalizedNames($data['name']);
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
