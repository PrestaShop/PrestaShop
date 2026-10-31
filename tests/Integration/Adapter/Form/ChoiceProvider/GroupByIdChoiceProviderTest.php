<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Form\ChoiceProvider;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider\GroupByIdChoiceProvider;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GroupByIdChoiceProviderTest extends KernelTestCase
{
    private const CONTEXT_LANG_ID = 1;
    private const DUPLICATE_NAME = 'Duplicate group name';

    private Connection $connection;
    private string $dbPrefix;
    private GroupByIdChoiceProvider $choiceProvider;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->dbPrefix = self::getContainer()->getParameter('database_prefix');
        $this->choiceProvider = new GroupByIdChoiceProvider(
            self::getContainer()->get(ConfigurationInterface::class),
            self::CONTEXT_LANG_ID
        );
    }

    /**
     * Symfony choice lists are keyed by the label, so building them straight from the group name drops
     * every group that shares a name with another one - the form then shows a single entry and saving it
     * applies the change to the last group only.
     */
    public function testGroupsSharingANameAreAllOffered(): void
    {
        $firstId = $this->insertGroup(self::DUPLICATE_NAME);
        $secondId = $this->insertGroup(self::DUPLICATE_NAME);

        try {
            $choices = $this->choiceProvider->getChoices();
            $ids = array_values($choices);

            $this->assertContains($firstId, $ids);
            $this->assertContains($secondId, $ids);
        } finally {
            $this->deleteGroups([$firstId, $secondId]);
        }
    }

    /**
     * The duplicates have to stay tellable apart in the list, otherwise the employee cannot know which
     * of the two rows they are editing.
     */
    public function testGroupsSharingANameAreLabelledWithTheirId(): void
    {
        $firstId = $this->insertGroup(self::DUPLICATE_NAME);
        $secondId = $this->insertGroup(self::DUPLICATE_NAME);

        try {
            $choices = $this->choiceProvider->getChoices();

            $this->assertArrayHasKey(sprintf('%s (%d)', self::DUPLICATE_NAME, $firstId), $choices);
            $this->assertArrayHasKey(sprintf('%s (%d)', self::DUPLICATE_NAME, $secondId), $choices);
            $this->assertArrayNotHasKey(self::DUPLICATE_NAME, $choices);
        } finally {
            $this->deleteGroups([$firstId, $secondId]);
        }
    }

    /**
     * A group with a name of its own keeps that name as its label.
     */
    public function testGroupWithAUniqueNameKeepsItsPlainName(): void
    {
        $groupId = $this->insertGroup('Unique group name');

        try {
            $choices = $this->choiceProvider->getChoices();

            $this->assertSame($groupId, $choices['Unique group name'] ?? null);
        } finally {
            $this->deleteGroups([$groupId]);
        }
    }

    private function insertGroup(string $name): int
    {
        $this->connection->executeStatement(
            'INSERT INTO ' . $this->dbPrefix . 'group (reduction, price_display_method, show_prices, date_add, date_upd)
             VALUES (0, 0, 1, NOW(), NOW())'
        );
        $groupId = (int) $this->connection->lastInsertId();

        $this->connection->executeStatement(
            'INSERT INTO ' . $this->dbPrefix . 'group_lang (id_group, id_lang, name) VALUES (:group, :lang, :name)',
            ['group' => $groupId, 'lang' => self::CONTEXT_LANG_ID, 'name' => $name]
        );
        $this->connection->executeStatement(
            'INSERT INTO ' . $this->dbPrefix . 'group_shop (id_group, id_shop) VALUES (:group, 1)',
            ['group' => $groupId]
        );

        return $groupId;
    }

    /**
     * @param int[] $groupIds
     */
    private function deleteGroups(array $groupIds): void
    {
        foreach (['group_shop', 'group_lang', 'group'] as $table) {
            $this->connection->executeStatement(
                'DELETE FROM ' . $this->dbPrefix . $table . ' WHERE id_group IN (:ids)',
                ['ids' => $groupIds],
                ['ids' => Connection::PARAM_INT_ARRAY]
            );
        }
    }
}
