<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Unit\PrestaShopBundle\Controller\Admin;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\ActivityLog\AdminActivity;
use PrestaShop\PrestaShop\Core\ActivityLog\AdminActivityType;
use PrestaShopBundle\Controller\Admin\PrestaShopAdminController;

class PrestaShopAdminControllerActivityLogTest extends TestCase
{
    public function testLogAdminActivitiesForwardsEveryActivityInOrder(): void
    {
        $firstActivity = new AdminActivity(
            AdminActivityType::DELETE,
            'Product',
            42,
            42
        );
        $secondActivity = new AdminActivity(
            AdminActivityType::DELETE,
            'Product',
            84,
            84
        );

        $controller = new TestablePrestaShopAdminController();
        $controller->logActivities([
            $firstActivity,
            $secondActivity,
        ]);

        $this->assertSame(
            [$firstActivity, $secondActivity],
            $controller->getLoggedActivities()
        );
    }
}

class TestablePrestaShopAdminController extends PrestaShopAdminController
{
    /**
     * @var AdminActivity[]
     */
    private array $loggedActivities = [];

    /**
     * @param AdminActivity[] $activities
     */
    public function logActivities(array $activities): void
    {
        $this->logAdminActivities($activities);
    }

    protected function logAdminActivity(AdminActivity $activity): void
    {
        $this->loggedActivities[] = $activity;
    }

    /**
     * @return AdminActivity[]
     */
    public function getLoggedActivities(): array
    {
        return $this->loggedActivities;
    }
}
