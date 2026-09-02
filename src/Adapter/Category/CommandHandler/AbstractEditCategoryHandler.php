<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Category\CommandHandler;

use Category;
use PrestaShop\PrestaShop\Adapter\Category\Repository\CategoryRepository;
use PrestaShop\PrestaShop\Adapter\Domain\AbstractObjectModelHandler;
use PrestaShop\PrestaShop\Adapter\Image\Uploader\CategoryImageUploader;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Category\Exception\CategoryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\CategoryId;
use PrestaShop\PrestaShop\Core\Domain\Category\ValueObject\RedirectOption;
use PrestaShop\PrestaShop\Core\Exception\CoreException;

/**
 * Class AbstractEditCategoryHandler.
 */
abstract class AbstractEditCategoryHandler extends AbstractObjectModelHandler
{
    public function __construct(
        protected readonly CategoryImageUploader $categoryImageUploader,
        private readonly CategoryRepository $categoryRepository,
    ) {
    }

    /**
     * @throws CategoryConstraintException
     * @throws CoreException
     * @throws CategoryNotFoundException
     */
    protected function fillWithRedirectOption(Category $category, RedirectOption $redirectOption): void
    {
        $redirectType = $redirectOption->getRedirectType();
        $redirectTarget = $redirectOption->getRedirectTarget();

        if ($redirectType->isCategoryType() && !$redirectTarget->isNoTarget()) {
            $this->categoryRepository->assertCategoryExists(new CategoryId($redirectTarget->getValue()));
        } elseif (!$redirectType->isCategoryType() && !$redirectTarget->isNoTarget()) {
            throw new CategoryConstraintException(sprintf(
                'Invalid redirect target "%d". This should have a value of 0 if the redirect type is "%d"',
                $redirectTarget->getValue(),
                $redirectType->getValue(),
            ),
                CategoryConstraintException::INVALID_REDIRECT_TARGET);
        }

        $category->redirect_type = $redirectType->getValue();
        $category->id_type_redirected = $redirectTarget->getValue();
    }
}
