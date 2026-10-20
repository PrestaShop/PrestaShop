<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Entity\Repository;

use Db;
use PrestaShopBundle\Entity\Repository\StockMovementRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Every stock movement keeps the name its employee had when it was recorded, so listing the filter
 * options with DISTINCT over that name put somebody in the dropdown once per name they had ever used,
 * every entry filtering on the same employee.
 */
class StockMovementEmployeeFilterTest extends KernelTestCase
{
    private const PRESENT_EMPLOYEE_ID = 1;
    private const DELETED_EMPLOYEE_ID = 999999;

    private StockMovementRepository $repository;

    /** @var int[] */
    private array $movementIds = [];

    private int $stockAvailableId;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->repository = self::$kernel->getContainer()->get('prestashop.core.api.stock_movement.repository');

        $this->stockAvailableId = (int) Db::getInstance()->getValue(
            'SELECT id_stock_available FROM ' . _DB_PREFIX_ . 'stock_available WHERE id_shop = 1'
        );
        self::assertGreaterThan(0, $this->stockAvailableId, 'the shop needs at least one stock row');
    }

    protected function tearDown(): void
    {
        if ($this->movementIds) {
            Db::getInstance()->execute(
                'DELETE FROM ' . _DB_PREFIX_ . 'stock_mvt WHERE id_stock_mvt IN (' . implode(',', $this->movementIds) . ')'
            );
        }
        $this->movementIds = [];

        parent::tearDown();
    }

    public function testAnEmployeeWhoWasRenamedIsListedOnce(): void
    {
        $this->recordMovement(self::PRESENT_EMPLOYEE_ID, 'Hea', 'Florine');
        $this->recordMovement(self::PRESENT_EMPLOYEE_ID, 'Hlavacek', 'Daniel');

        $listed = $this->listedEmployeeIds();

        self::assertSame(
            [self::PRESENT_EMPLOYEE_ID],
            $listed,
            'the same employee was offered once per name they had used'
        );
    }

    /**
     * The one entry should say who they are now, not who they were when the oldest movement was saved.
     */
    public function testTheEntryUsesTheCurrentName(): void
    {
        $this->recordMovement(self::PRESENT_EMPLOYEE_ID, 'Some', 'Oldname');

        $current = Db::getInstance()->getRow(
            'SELECT lastname, firstname FROM ' . _DB_PREFIX_ . 'employee WHERE id_employee = ' . self::PRESENT_EMPLOYEE_ID
        );

        self::assertSame(
            trim($current['lastname'] . ' ' . $current['firstname']),
            $this->nameListedFor(self::PRESENT_EMPLOYEE_ID)
        );
    }

    /**
     * Employees are removed outright, so their movements outlive them and the recorded name is all
     * there is left to show.
     */
    public function testAMovementFromADeletedEmployeeKeepsItsRecordedName(): void
    {
        $this->recordMovement(self::DELETED_EMPLOYEE_ID, 'Gone', 'Employee');

        self::assertSame('Gone Employee', $this->nameListedFor(self::DELETED_EMPLOYEE_ID));
    }

    /**
     * @return int[]
     */
    private function listedEmployeeIds(): array
    {
        $ids = [];
        foreach ($this->repository->getEmployees() as $row) {
            if (in_array((int) $row['id_employee'], [self::PRESENT_EMPLOYEE_ID, self::DELETED_EMPLOYEE_ID], true)) {
                $ids[] = (int) $row['id_employee'];
            }
        }

        return $ids;
    }

    private function nameListedFor(int $employeeId): ?string
    {
        foreach ($this->repository->getEmployees() as $row) {
            if ((int) $row['id_employee'] === $employeeId) {
                return $row['name'];
            }
        }

        return null;
    }

    private function recordMovement(int $employeeId, string $lastname, string $firstname): void
    {
        Db::getInstance()->insert('stock_mvt', [
            'id_stock' => $this->stockAvailableId,
            'id_order' => 0,
            'id_supply_order' => 0,
            'id_stock_mvt_reason' => 1,
            'id_employee' => $employeeId,
            'employee_lastname' => pSQL($lastname),
            'employee_firstname' => pSQL($firstname),
            'physical_quantity' => 1,
            'date_add' => date('Y-m-d H:i:s'),
            'sign' => 1,
        ]);

        $this->movementIds[] = (int) Db::getInstance()->Insert_ID();
    }
}
