<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
            1 => 'name with >',
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
