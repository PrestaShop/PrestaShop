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

namespace Tests\Unit\Core\Domain\CmsPageCategory\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\EditCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;

class EditCmsPageCategoryCommandTest extends TestCase
{
    public function testItThrowsAnExceptionWhenCmsCategoryNamedIsIncorrect()
    {
        $this->expectException(CmsPageCategoryConstraintException::class);
        $this->expectExceptionCode(CmsPageCategoryConstraintException::INVALID_CATEGORY_NAME);

        $command = new EditCmsPageCategoryCommand(1);

        $command->setLocalisedName([
            1 => 'name with #',
        ]);
    }

    public function testItThrowsAnExceptionWhenIncorrectTypeIdIsPassed()
    {
        $this->expectException(CmsPageCategoryException::class);

        $incorrectTypeId = '1';
        /** @phpstan-ignore-next-line */
        $command = new EditCmsPageCategoryCommand($incorrectTypeId);
    }

    public function testItThrowsAnExceptionWhenIncorrectTypeIdIsPassedForCategoryParent()
    {
        $this->expectException(CmsPageCategoryException::class);

        $incorrectTypeId = '1';
        $command = new EditCmsPageCategoryCommand(1);

        /* @phpstan-ignore-next-line */
        $command->setParentId($incorrectTypeId);
    }

    public function testItThrowsAnExceptionWhenMetaTitleIsIncorrect()
    {
        $this->expectException(CmsPageCategoryConstraintException::class);
        $this->expectExceptionCode(CmsPageCategoryConstraintException::INVALID_META_TITLE);

        $command = new EditCmsPageCategoryCommand(1);

        $command->setLocalisedMetaTitle([
            1 => '{object}',
        ]);
    }

    public function testItThrowsAnExceptionWhenMetaKeywordsIsIncorrect()
    {
        $this->expectException(CmsPageCategoryConstraintException::class);
        $this->expectExceptionCode(CmsPageCategoryConstraintException::INVALID_META_KEYWORDS);

        $command = new EditCmsPageCategoryCommand(1);

        $command->setLocalisedMetaKeywords([
            1 => '{object}',
        ]);
    }

    public function testItThrowsAnExceptionWhenMetaDescriptionIsIncorrect()
    {
        $this->expectException(CmsPageCategoryConstraintException::class);
        $this->expectExceptionCode(CmsPageCategoryConstraintException::INVALID_META_DESCRIPTION);

        $command = new EditCmsPageCategoryCommand(1);

        $command->setLocalisedMetaDescription([
            1 => '{object}',
        ]);
    }
}
