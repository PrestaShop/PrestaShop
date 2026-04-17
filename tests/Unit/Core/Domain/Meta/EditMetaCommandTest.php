<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\Core\Domain\Meta;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\EditMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaException;

/**
 * Class EditMetaCommandTest
 */
class EditMetaCommandTest extends TestCase
{
    /**
     * @dataProvider getIncorrectIds
     */
    public function testItThrowsAnExceptionOnIncorrectMetaIdPassed($incorrectId)
    {
        $this->expectException(MetaException::class);
        $command = new EditMetaCommand($incorrectId);
    }

    /**
     * @dataProvider getIncorrectPageNames
     */
    public function testItThrowsAnExceptionOnIncorrectOrMissingPageName($incorrectPageName)
    {
        $this->expectException(MetaConstraintException::class);
        $this->expectExceptionCode(MetaConstraintException::INVALID_PAGE_NAME);

        $command = new EditMetaCommand(1);
        $command->setPageName($incorrectPageName);
    }

    /**
     * @dataProvider getIncorrectMultiLanguageNames
     */
    public function testItThrowsAnExceptionOnIncorrectPageTitle($incorrectNames)
    {
        $this->expectException(MetaConstraintException::class);
        $this->expectExceptionCode(MetaConstraintException::INVALID_PAGE_TITLE);

        $command = new EditMetaCommand(1);

        $command->setLocalisedPageTitles($incorrectNames);
    }

    /**
     * @dataProvider getIncorrectMultiLanguageNames
     */
    public function testItThrowsAnExceptionOnIncorrectPageDescription($incorrectNames)
    {
        $this->expectException(MetaConstraintException::class);
        $this->expectExceptionCode(MetaConstraintException::INVALID_META_DESCRIPTION);

        $command = new EditMetaCommand(1);

        $command->setLocalisedMetaDescriptions($incorrectNames);
    }

    public function getIncorrectPageNames()
    {
        return [
            [
                null,
            ],
            [
                '',
            ],
            [
                'wrong-page-name{}',
            ],
        ];
    }

    public function getIncorrectMultiLanguageNames()
    {
        return [
            [
                [
                    '#$%^@{}',
                ],
            ],
        ];
    }

    public function getIncorrectIds()
    {
        return [
            [
                '1',
            ],
            [
                -1,
            ],
            [
                0,
            ],
        ];
    }
}
