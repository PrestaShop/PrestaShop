<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Adapter\Csp\CspViolationRecorder;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\ClearCspLogCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\RecordCspViolationCommand;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShopBundle\Entity\CspLog;
use PrestaShopBundle\Entity\Repository\CspLogRepository;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;

class CspFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When /^I record a CSP violation for shop (\d+) with directive "([^"]*)" and blocked source "([^"]*)"$/
     */
    public function recordViolation(string $shopId, string $directive, string $source): void
    {
        $this->getCommandBus()->handle(new RecordCspViolationCommand(
            $directive,
            $source,
            null,
            (int) $shopId,
        ));
    }

    /**
     * @When /^I clear the CSP log for shop (\d+)$/
     */
    public function clearLogForShop(string $shopId): void
    {
        $this->getCommandBus()->handle(new ClearCspLogCommand(
            ShopConstraint::shop((int) $shopId),
        ));
    }

    /**
     * Records several violations in order through a recorder with a custom row cap. The cap is a
     * constructor argument of the recorder and never reachable through the command bus (which always
     * uses the default cap), so the collaborator is built here against the real repository. This is
     * the only step that does not go through the bus.
     *
     * @When /^I record the following CSP violations for shop (\d+) through a recorder capped at (\d+):$/
     */
    public function recordViolationsThroughCappedRecorder(string $shopId, string $cap, TableNode $table): void
    {
        $recorder = new CspViolationRecorder($this->getCspLogRepository(), (int) $cap);

        foreach ($table->getColumnsHash() as $row) {
            $recorder->record((int) $shopId, $row['directive'], $row['source'], null);
        }
    }

    /**
     * @Then /^the CSP log for shop (\d+) should contain (\d+) rows?$/
     */
    public function assertRowCount(string $shopId, string $expectedCount): void
    {
        $actual = $this->getCspLogRepository()->countByShop((int) $shopId);

        if ($actual !== (int) $expectedCount) {
            throw new RuntimeException(sprintf(
                'Expected %d row(s) in CSP log for shop %d, found %d',
                (int) $expectedCount,
                (int) $shopId,
                $actual,
            ));
        }
    }

    /**
     * @Then /^the CSP log for shop (\d+) should be empty$/
     */
    public function assertLogEmpty(string $shopId): void
    {
        $this->assertRowCount($shopId, '0');
    }

    /**
     * @Then /^violation "([^"]*)" from "([^"]*)" for shop (\d+) should have (\d+) hits?$/
     */
    public function assertViolationHits(string $directive, string $source, string $shopId, string $expectedHits): void
    {
        $log = $this->findLog((int) $shopId, $directive, $source);

        if (null === $log) {
            throw new RuntimeException(sprintf(
                'Expected a CSP violation "%s" from "%s" for shop %d, none was recorded',
                $directive,
                $source,
                (int) $shopId,
            ));
        }

        if ($log->getHits() !== (int) $expectedHits) {
            throw new RuntimeException(sprintf(
                'Expected %d hit(s) on CSP violation "%s" from "%s" for shop %d, found %d',
                (int) $expectedHits,
                $directive,
                $source,
                (int) $shopId,
                $log->getHits(),
            ));
        }
    }

    /**
     * @Then /^the CSP log for shop (\d+) should not contain violation "([^"]*)" from "([^"]*)"$/
     */
    public function assertNoViolation(string $shopId, string $directive, string $source): void
    {
        if (null !== $this->findLog((int) $shopId, $directive, $source)) {
            throw new RuntimeException(sprintf(
                'Expected no CSP violation "%s" from "%s" for shop %d, but one was found',
                $directive,
                $source,
                (int) $shopId,
            ));
        }
    }

    private function findLog(int $shopId, string $directive, string $source): ?CspLog
    {
        return $this->getCspLogRepository()->findOneBy([
            'shopId' => $shopId,
            'directive' => $directive,
            'source' => $source,
        ]);
    }

    private function getCspLogRepository(): CspLogRepository
    {
        return CommonFeatureContext::getContainer()->get(CspLogRepository::class);
    }
}
