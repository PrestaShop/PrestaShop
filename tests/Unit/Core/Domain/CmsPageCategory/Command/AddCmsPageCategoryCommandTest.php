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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Unit\Core\Domain\CmsPageCategory\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Command\AddCmsPageCategoryCommand;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryConstraintException;
use PrestaShop\PrestaShop\Core\Domain\CmsPageCategory\Exception\CmsPageCategoryException;

class AddCmsPageCategoryCommandTest extends TestCase
{
    public function testItThrowsAnExceptionWhenCmsCategoryNamedIsIncorrect()
    {
        $this->expectException(CmsPageCategoryConstraintException::class);
        $this->expectExceptionCode(CmsPageCategoryConstraintException::INVALID_CATEGORY_NAME);

        $incorrectName = [
            1 => 'hashtag #',
        ];

        $command = new AddCmsPageCategoryCommand(
            $incorrectName,
            [
                1 => 'hashtag',
            ],
            1,
            true
        );
    }

    public function testItThrowsAnExceptionWhenIncorrectTypeIdIsPassedForCategoryParent()
    {
        $this->expectException(CmsPageCategoryException::class);

        $incorrectId = '1';
        $command = new AddCmsPageCategoryCommand(
            [
                1 => 'hashtag',
            ],
            [
                1 => 'hashtag',
            ],
            $incorrectId,
            true
        );
    }

    public function testItThrowsAnExceptionWhenMetaTitleIsIncorrect()
    {
        $this->expectException(CmsPageCategoryConstraintException::class);
        $this->expectExceptionCode(CmsPageCategoryConstraintException::INVALID_META_TITLE);

        $command = new AddCmsPageCategoryCommand([], [], 1, false);

        $command->setLocalisedMetaTitle([
            1 => '{object}',
        ]);
    }

    public function testItThrowsAnExceptionWhenMetaKeywordsIsIncorrect()
    {
        $this->expectException(CmsPageCategoryConstraintException::class);
        $this->expectExceptionCode(CmsPageCategoryConstraintException::INVALID_META_KEYWORDS);

        $command = new AddCmsPageCategoryCommand([], [], 1, false);

        $command->setLocalisedMetaKeywords([
            1 => '<object>',
        ]);
    }

    public function testItThrowsAnExceptionWhenMetaDescriptionIsIncorrect()
    {
        $this->expectException(CmsPageCategoryConstraintException::class);
        $this->expectExceptionCode(CmsPageCategoryConstraintException::INVALID_META_DESCRIPTION);

        $command = new AddCmsPageCategoryCommand([], [], 1, false);

        $command->setLocalisedMetaDescription([
            1 => '=object=',
        ]);
    }
}
