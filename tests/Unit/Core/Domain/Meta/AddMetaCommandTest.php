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

namespace Tests\Unit\Core\Domain\Meta;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Meta\Command\AddMetaCommand;
use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;

/**
 * Class AddMetaCommandTest
 */
class AddMetaCommandTest extends TestCase
{
    /**
     * @dataProvider getIncorrectPageNames
     */
    public function testItThrowsAnExceptionOnIncorrectOrMissingPageName($incorrectPageName)
    {
        $this->expectException(MetaConstraintException::class);
        $this->expectExceptionCode(MetaConstraintException::INVALID_PAGE_NAME);

        $command = new AddMetaCommand($incorrectPageName);
    }

    /**
     * @dataProvider getIncorrectMultiLanguageNames
     */
    public function testItThrowsAnExceptionOnIncorrectPageTitle($incorrectNames)
    {
        $this->expectException(MetaConstraintException::class);
        $this->expectExceptionCode(MetaConstraintException::INVALID_PAGE_TITLE);

        $command = new AddMetaCommand('correct-page-name');

        $command->setLocalisedPageTitle($incorrectNames);
    }

    /**
     * @dataProvider getIncorrectMultiLanguageNames
     */
    public function testItThrowsAnExceptionOnIncorrectPageDescription($incorrectNames)
    {
        $this->expectException(MetaConstraintException::class);
        $this->expectExceptionCode(MetaConstraintException::INVALID_META_DESCRIPTION);

        $command = new AddMetaCommand('correct-page-name');

        $command->setLocalisedMetaDescription($incorrectNames);
    }

    /**
     * @dataProvider getIncorrectMultiLanguageNames
     */
    public function testItThrowsAnExceptionOnIncorrectMetaKeywords($incorrectNames)
    {
        $this->expectException(MetaConstraintException::class);
        $this->expectExceptionCode(MetaConstraintException::INVALID_META_KEYWORDS);

        $command = new AddMetaCommand('correct-page-name');

        $command->setLocalisedMetaKeywords($incorrectNames);
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
}
