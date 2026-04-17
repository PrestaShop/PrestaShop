<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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

        $command = new AddCmsPageCategoryCommand(
            [
                1 => 'somecategoryname >',
            ],
            [
                1 => 'somecategoryname',
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
                1 => 'somecategoryname',
            ],
            [
                1 => 'somecategoryname',
            ],
            /* @phpstan-ignore-next-line */
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

    public function testItThrowsAnExceptionWhenMetaDescriptionIsIncorrect()
    {
        $this->expectException(CmsPageCategoryConstraintException::class);
        $this->expectExceptionCode(CmsPageCategoryConstraintException::INVALID_META_DESCRIPTION);

        $command = new AddCmsPageCategoryCommand([], [], 1, false);

        $command->setLocalisedMetaDescription([
            1 => '{object}',
        ]);
    }
}
