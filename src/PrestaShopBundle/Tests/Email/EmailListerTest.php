<?php
/**
 * 2007-2016 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @copyright 2007-2016 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Tests\Email;

use PrestaShopBundle\Email\EmailLister;
use PrestaShopBundle\Filesystem\Filesystem;

class EmailListerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EmailLister
     */
    private $emailLister;

    public function setUp()
    {
        $filesystem = new Filesystem();
        $this->emailLister = new EmailLister($filesystem);
    }

    public function testGetAvailableMails()
    {
        $emailDir = _PS_MAIL_DIR_ . 'en' . DIRECTORY_SEPARATOR;
        $availableEmails = $this->emailLister->getAvailableMails($emailDir);

        $expectedEmailsCount = 33;
        $this->assertCount($expectedEmailsCount, $availableEmails,
            sprintf('There should be %d availalbe emails', $expectedEmailsCount));

        return $availableEmails;
    }

    /**
     * @param $availableEmails
     * @depends testGetAvailableMails
     */
    public function testGetCleanedMailName($availableEmails)
    {
        $lastAvailableMail = $availableEmails[count($availableEmails) - 1];
        $cleanedMail = $this->emailLister->getCleanedMailName($lastAvailableMail);

        $this->assertNotContains('_', $cleanedMail, 'It should not contain underscores');
    }
}
