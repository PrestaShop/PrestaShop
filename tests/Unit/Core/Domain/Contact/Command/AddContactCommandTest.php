<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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

namespace Tests\Unit\Core\Domain\Contact\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\AddContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\Exception\ContactConstraintException;

/**
 * Class AddContactCommandTest
 */
class AddContactCommandTest extends TestCase
{
    /**
     * @dataProvider getIncorrectTitles
     *
     * @param $incorrectTitle
     */
    public function testItThrowsAnExceptionOnIncorrectTitle($incorrectTitle)
    {
        $this->expectException(ContactConstraintException::class);
        $this->expectExceptionCode(ContactConstraintException::INVALID_TITLE);

        $command = new AddContactCommand($incorrectTitle, false);
    }

    public function getIncorrectTitles()
    {
        return [
            [
                [],
            ],
            [
                [
                    '',
                    null,
                    true,
                ],
            ],
            [
                [
                    '{}',
                    'test<=',
                ],
            ],
        ];
    }

    /**
     * @dataProvider getIncorrectShopAssociations
     */
    public function testItThrowsAnExceptionOnIncorrectShopAssociation($incorrectShopAssociation)
    {
        $this->expectException(ContactConstraintException::class);
        $this->expectExceptionCode(ContactConstraintException::INVALID_SHOP_ASSOCIATION);

        $command = new AddContactCommand(
            [
                'test title',
            ],
            false
        );

        $command->setShopAssociation($incorrectShopAssociation);
    }

    public function getIncorrectShopAssociations()
    {
        return [
            [
                [
                    '1',
                ],
            ],
            [
                [
                    null,
                    false,
                ],
            ],
        ];
    }
}
