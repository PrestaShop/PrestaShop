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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Command\AddRootCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\Category\CommandHandler\AddRootCategoryHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CannotAddCategoryException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;

/**
 * Class AddRootCategoryHandler.
 */
final class AddRootCategoryHandler extends AbstractCategoryHandler implements AddRootCategoryHandlerInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ImageUploaderInterface $categoryCoverUploader
     * @param ImageUploaderInterface $categoryThumbnailUploader
     * @param ImageUploaderInterface $categoryMenuThumbnailUploader
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        ImageUploaderInterface $categoryCoverUploader,
        ImageUploaderInterface $categoryThumbnailUploader,
        ImageUploaderInterface $categoryMenuThumbnailUploader,
        ConfigurationInterface $configuration
    ) {
        parent::__construct(
            $categoryCoverUploader,
            $categoryThumbnailUploader,
            $categoryMenuThumbnailUploader
        );

        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     *
     * @throws CannotAddCategoryException
     */
    public function handle(AddRootCategoryCommand $command)
    {
        $category = new Category();
        $category->is_root_category = true;
        $category->level_depth = 1;
        $category->id_parent = $this->configuration->get('PS_ROOT_CATEGORY');

        $this->populateCategoryWithCommandData($category, $command);

        $category->add();

        if (!$category->id) {
            throw new CannotAddCategoryException('Failed to add new category.');
        }

        $this->uploadImages($category, $command);

        return new CategoryId($category->id);
    }
}
