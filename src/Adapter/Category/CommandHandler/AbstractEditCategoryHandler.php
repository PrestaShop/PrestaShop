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

        if ($redirectType->isCategoryType()) {
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
