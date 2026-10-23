<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Form\Admin\Sell\Customer;

use Doctrine\DBAL\Connection;
use PrestaShopBundle\Form\Admin\Sell\Customer\CustomerType;
use Tests\Integration\PrestaShopBundle\Form\FormListenerTestCase;
use Tests\Resources\DatabaseDump;

/**
 * @see https://github.com/PrestaShop/PrestaShop/issues/38713
 */
class CustomerTypeTest extends FormListenerTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['gender', 'gender_lang']);
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['gender', 'gender_lang']);
    }

    public function testSocialTitleFieldIsDisplayedWhenSocialTitlesExist(): void
    {
        $form = $this->createForm(CustomerType::class);

        $this->assertFormTypeExistsInForm($form, 'gender_id', true);
        $this->assertCount(
            $this->countSocialTitles(),
            $form->get('gender_id')->getConfig()->getOption('choices')
        );
    }

    public function testSocialTitleFieldIsNotDisplayedWithoutSocialTitles(): void
    {
        $this->deleteAllSocialTitles();
        $this->assertSame(0, $this->countSocialTitles());

        $form = $this->createForm(CustomerType::class);

        $this->assertFormTypeExistsInForm($form, 'gender_id', false);
        $this->assertFormTypeExistsInForm($form, 'first_name', true);
    }

    private function countSocialTitles(): int
    {
        return (int) $this->getConnection()->createQueryBuilder()
            ->select('COUNT(*)')
            ->from($this->getDbPrefix() . 'gender')
            ->executeQuery()
            ->fetchOne()
        ;
    }

    private function deleteAllSocialTitles(): void
    {
        $connection = $this->getConnection();
        $connection->createQueryBuilder()->delete($this->getDbPrefix() . 'gender_lang')->executeStatement();
        $connection->createQueryBuilder()->delete($this->getDbPrefix() . 'gender')->executeStatement();
    }

    private function getConnection(): Connection
    {
        return self::getContainer()->get('doctrine.dbal.default_connection');
    }

    private function getDbPrefix(): string
    {
        return self::getContainer()->getParameter('database_prefix');
    }
}
