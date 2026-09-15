<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Contact;
use Db;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Contact\Command\AddContactCommand;
use PrestaShop\PrestaShop\Core\Domain\Contact\ValueObject\ContactId;
use PrestaShop\PrestaShop\Core\Grid\GridFactoryInterface;
use PrestaShop\PrestaShop\Core\Grid\Presenter\GridPresenterInterface;
use PrestaShop\PrestaShop\Core\Grid\Query\DoctrineQueryBuilderInterface;
use PrestaShop\PrestaShop\Core\Search\Filters\ContactFilters;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * Contacts are listed in the order the merchant arranged them, the name is only a tie-breaker.
 */
class ContactPositionTest extends KernelTestCase
{
    private static int $langId;

    /**
     * Positions as they were before this test class ran, id_contact => position.
     *
     * @var array<int, int>
     */
    private static array $initialPositions = [];

    /**
     * @var int[]
     */
    private array $createdContactIds = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();

        self::$langId = (int) Configuration::get('PS_LANG_DEFAULT');
        self::$initialPositions = self::readPositions();
    }

    public static function tearDownAfterClass(): void
    {
        foreach (self::$initialPositions as $contactId => $position) {
            Db::getInstance()->update('contact', ['position' => $position], 'id_contact = ' . $contactId);
        }

        parent::tearDownAfterClass();
    }

    protected function tearDown(): void
    {
        foreach ($this->createdContactIds as $contactId) {
            $contact = new Contact($contactId);

            if ($contact->id) {
                $contact->delete();
            }
        }
        $this->createdContactIds = [];

        parent::tearDown();
    }

    public function testNewContactIsAppendedAtTheEndOfTheList(): void
    {
        $highestPosition = Contact::getHighestPosition();

        $contact = $this->createContact('Zulu');

        $this->assertSame($highestPosition + 1, (int) $contact->position);
    }

    /**
     * The back office creates contacts through the command bus, not through the ObjectModel directly.
     */
    public function testContactAddedThroughTheCommandBusIsAppendedAtTheEndOfTheList(): void
    {
        // No HTTP request ran, so nothing initialized the language context the command bus needs.
        $languageContextBuilder = self::getContainer()->get(LanguageContextBuilder::class);
        $languageContextBuilder->setLanguageId(self::$langId);
        $languageContextBuilder->setDefaultLanguageId(self::$langId);

        $highestPosition = Contact::getHighestPosition();

        $command = new AddContactCommand([self::$langId => 'Zulu'], false);
        $command->setShopAssociation([(int) Configuration::get('PS_SHOP_DEFAULT')]);

        /** @var ContactId $contactId */
        $contactId = self::getContainer()->get('prestashop.core.command_bus')->handle($command);
        $this->createdContactIds[] = $contactId->getValue();

        $this->assertSame($highestPosition + 1, (int) (new Contact($contactId->getValue()))->position);
    }

    public function testGetContactsIsOrderedByPositionAndNotByName(): void
    {
        // Created in reverse alphabetical order, so the position order and the name order disagree.
        $zulu = $this->createContact('Zulu');
        $mike = $this->createContact('Mike');
        $alpha = $this->createContact('Alpha');

        $this->assertSame(
            [(int) $zulu->id, (int) $mike->id, (int) $alpha->id],
            $this->listedContactIds()
        );
    }

    public function testContactsSharingAPositionFallBackOnTheirName(): void
    {
        $zulu = $this->createContact('Zulu');
        $alpha = $this->createContact('Alpha');

        Db::getInstance()->update('contact', ['position' => 500], 'id_contact IN (' . (int) $zulu->id . ', ' . (int) $alpha->id . ')');

        $this->assertSame(
            [(int) $alpha->id, (int) $zulu->id],
            $this->listedContactIds()
        );
    }

    public function testDeletingAContactCompactsThePositionsOfTheOthers(): void
    {
        $zulu = $this->createContact('Zulu');
        $mike = $this->createContact('Mike');
        $alpha = $this->createContact('Alpha');

        $mike->delete();
        $this->createdContactIds = array_values(array_diff($this->createdContactIds, [(int) $mike->id]));

        $positions = self::readPositions();

        $this->assertSame(range(0, count($positions) - 1), array_values($positions));
        $this->assertSame([(int) $zulu->id, (int) $alpha->id], $this->listedContactIds());
    }

    /**
     * The drag handle is only added by the grid presenter when the grid is sorted on the
     * position column, so the default sort order is what makes reordering reachable at all.
     */
    public function testContactsGridIsSortedOnPositionSoTheDragHandleIsRendered(): void
    {
        $this->assertSame('position', ContactFilters::getDefaults()['orderBy']);

        $columnTypes = array_column($this->presentDefaultGrid()['columns'], 'type', 'id');

        $this->assertSame('position', $columnTypes['position'] ?? null);
        $this->assertSame('position_handle', $columnTypes['position_handle'] ?? null);
    }

    /**
     * Shops upgraded from a version that never maintained the column have every contact at position 0.
     * Sorting on the position alone leaves their order up to the database, and the position update handler
     * re-indexes the rows it is given by the order they come back in. Asserted on the query rather than on
     * an observed row order: with equal sort keys the server is free to return any order, so a run that
     * happens to come back by id proves nothing.
     */
    public function testContactsGridQueryIsUnambiguouslyOrdered(): void
    {
        /** @var DoctrineQueryBuilderInterface $queryBuilder */
        $queryBuilder = self::getContainer()->get('prestashop.core.grid.query_builder.contact');
        $sql = $queryBuilder->getSearchQueryBuilder(new ContactFilters(ContactFilters::getDefaults()))->getSQL();

        $this->assertMatchesRegularExpression('/ORDER BY [^)]*\bc\.id_contact ASC/i', $sql);
    }

    /**
     * @return array<string, mixed>
     */
    private function presentDefaultGrid(): array
    {
        // The grid factory reads the filter form from the session; a kernel test has no request.
        $request = new Request();
        $request->setSession(new Session(new MockArraySessionStorage()));
        $requestStack = self::getContainer()->get('request_stack');
        $requestStack->push($request);

        /** @var GridFactoryInterface $gridFactory */
        $gridFactory = self::getContainer()->get('prestashop.core.grid.factory.contacts');
        /** @var GridPresenterInterface $gridPresenter */
        $gridPresenter = self::getContainer()->get(GridPresenterInterface::class);

        try {
            return $gridPresenter->present($gridFactory->getGrid(new ContactFilters(ContactFilters::getDefaults())));
        } finally {
            $requestStack->pop();
        }
    }

    /**
     * Contact ids created by the current test, in the order Contact::getContacts() returns them.
     *
     * @return int[]
     */
    private function listedContactIds(): array
    {
        $listedIds = array_map(
            static fn (array $contact): int => (int) $contact['id_contact'],
            Contact::getContacts(self::$langId)
        );

        return array_values(array_intersect($listedIds, $this->createdContactIds));
    }

    private function createContact(string $name): Contact
    {
        $contact = new Contact();
        $contact->name = [self::$langId => $name];
        $contact->email = 'position-test@prestashop.com';
        $contact->customer_service = false;

        $contact->add();
        $contact->associateTo((int) Configuration::get('PS_SHOP_DEFAULT'));

        $this->createdContactIds[] = (int) $contact->id;

        return $contact;
    }

    /**
     * @return array<int, int> id_contact => position, ordered by position
     */
    private static function readPositions(): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `id_contact`, `position` FROM `' . _DB_PREFIX_ . 'contact` ORDER BY `position` ASC, `id_contact` ASC'
        );

        $positions = [];
        foreach ($rows as $row) {
            $positions[(int) $row['id_contact']] = (int) $row['position'];
        }

        return $positions;
    }
}
