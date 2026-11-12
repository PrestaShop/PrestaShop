<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Helper;

use HelperList;
use PHPUnit\Framework\TestCase;

class HelperListTest extends TestCase
{
    private const SKIPPED_ID = 1;
    private const ALLOWED_ID = 2;

    private function helper(array $skipActions): HelperListLinkProbe
    {
        $helper = new HelperListLinkProbe();

        $helper->table = 'cms';
        $helper->identifier = 'id_cms';
        $helper->token = 'a-token';
        $helper->currentIndex = 'index.php?controller=AdminCmsContent';
        $helper->list_skip_actions = $skipActions;

        return $helper;
    }

    public function testAnAllowedRowKeepsItsViewAndEditLink(): void
    {
        $helper = $this->helper([]);

        $this->assertNotSame('', $helper->viewLink(null, self::ALLOWED_ID));
        $this->assertNotSame('', $helper->editLink(null, self::ALLOWED_ID));
    }

    /**
     * The row itself is clickable and navigates to this link, so handing one back for an action the
     * list declared unavailable is what made the row lead to a page the buttons deliberately hid.
     */
    public function testARowWithASkippedViewActionGetsNoViewLink(): void
    {
        $helper = $this->helper(['view' => [self::SKIPPED_ID]]);

        $this->assertSame('', $helper->viewLink(null, self::SKIPPED_ID));
        $this->assertNotSame('', $helper->viewLink(null, self::ALLOWED_ID));
    }

    public function testARowWithASkippedEditActionGetsNoEditLink(): void
    {
        $helper = $this->helper(['edit' => [self::SKIPPED_ID]]);

        $this->assertSame('', $helper->editLink(null, self::SKIPPED_ID));
        $this->assertNotSame('', $helper->editLink(null, self::ALLOWED_ID));
    }

    public function testSkippingOneActionLeavesTheOtherAlone(): void
    {
        $helper = $this->helper(['view' => [self::SKIPPED_ID]]);

        $this->assertSame('', $helper->viewLink(null, self::SKIPPED_ID));
        $this->assertNotSame('', $helper->editLink(null, self::SKIPPED_ID));
    }

    public function testTheSkipPredicate(): void
    {
        $helper = $this->helper(['view' => [self::SKIPPED_ID]]);

        $this->assertTrue($helper->actionSkipped('view', self::SKIPPED_ID));
        $this->assertFalse($helper->actionSkipped('view', self::ALLOWED_ID));
        $this->assertFalse($helper->actionSkipped('edit', self::SKIPPED_ID));
        $this->assertFalse($helper->actionSkipped('delete', self::SKIPPED_ID));
    }
}

/**
 * Exposes the protected link builders so the test can call them directly.
 */
class HelperListLinkProbe extends HelperList
{
    public function viewLink(?string $token, int $id): string
    {
        return $this->getViewLink($token, $id);
    }

    public function editLink(?string $token, int $id): string
    {
        return $this->getEditLink($token, $id);
    }

    public function actionSkipped(string $action, int $id): bool
    {
        return $this->isActionSkipped($action, $id);
    }
}
