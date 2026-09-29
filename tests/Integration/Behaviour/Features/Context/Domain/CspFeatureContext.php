<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use PrestaShop\PrestaShop\Adapter\Csp\CspViolationRecorder;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\AddCspRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\AllowCspSourceCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\BulkRevokeCspSourceCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\ClearCspLogCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\RecordCspViolationCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Command\RevokeCspSourceCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Csp\Exception\CspException;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspRuleId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Search\Filters\CspLogFilters;
use PrestaShopBundle\Entity\CspLog;
use PrestaShopBundle\Entity\Repository\CspLogRepository;
use PrestaShopBundle\Entity\Repository\CspRuleRepository;
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

    /**
     * @When /^I add a CSP rule "([^"]*)" for shop (\d+) with directive "([^"]*)" and source "([^"]*)"$/
     */
    public function addRule(string $ruleReference, string $shopId, string $directive, string $source): void
    {
        try {
            /** @var CspRuleId $ruleId */
            $ruleId = $this->getCommandBus()->handle(new AddCspRuleCommand(
                $directive,
                $source,
                ShopConstraint::shop((int) $shopId),
            ));

            $this->getSharedStorage()->set($ruleReference, $ruleId->getValue());
        } catch (CspConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then /^I should get a CSP error that the rule already exists$/
     */
    public function assertDuplicateRuleError(): void
    {
        $this->assertLastErrorIs(CspConstraintException::class, CspConstraintException::DUPLICATE_RULE);
    }

    /**
     * @When /^I allow the recorded violation "([^"]*)" from "([^"]*)" for shop (\d+) as CSP rule "([^"]*)"$/
     */
    public function allowViolation(string $directive, string $source, string $shopId, string $ruleReference): void
    {
        $log = $this->findLog((int) $shopId, $directive, $source);

        if (null === $log) {
            throw new RuntimeException(sprintf(
                'Expected a recorded CSP violation "%s" from "%s" for shop %d to allow, none was found',
                $directive,
                $source,
                (int) $shopId,
            ));
        }

        /** @var CspRuleId $ruleId */
        $ruleId = $this->getCommandBus()->handle(new AllowCspSourceCommand($log->getId(), ShopConstraint::shop((int) $shopId)));

        $this->getSharedStorage()->set($ruleReference, $ruleId->getValue());
    }

    /**
     * @When /^I try to allow the recorded violation "([^"]*)" from "([^"]*)" for shop (\d+) while scoped to shop (\d+)$/
     */
    public function allowViolationOutOfScope(string $directive, string $source, string $logShopId, string $scopeShopId): void
    {
        $log = $this->findLog((int) $logShopId, $directive, $source);

        if (null === $log) {
            throw new RuntimeException(sprintf(
                'Expected a recorded CSP violation "%s" from "%s" for shop %d to allow, none was found',
                $directive,
                $source,
                (int) $logShopId,
            ));
        }

        try {
            $this->getCommandBus()->handle(new AllowCspSourceCommand($log->getId(), ShopConstraint::shop((int) $scopeShopId)));
        } catch (CspException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I revoke CSP rule "([^"]*)"$/
     */
    public function revokeRule(string $ruleReference): void
    {
        $ruleId = $this->referenceToId($ruleReference);
        $rule = $this->getCspRuleRepository()->getById($ruleId);
        $this->getCommandBus()->handle(new RevokeCspSourceCommand($ruleId, ShopConstraint::shop($rule->getShopId())));
    }

    /**
     * @When /^I try to revoke CSP rule "([^"]*)" while scoped to shop (\d+)$/
     */
    public function revokeRuleOutOfScope(string $ruleReference, string $scopeShopId): void
    {
        try {
            $this->getCommandBus()->handle(new RevokeCspSourceCommand($this->referenceToId($ruleReference), ShopConstraint::shop((int) $scopeShopId)));
        } catch (CspException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I bulk revoke CSP rules "([^"]*)"$/
     */
    public function bulkRevokeRules(string $ruleReferences): void
    {
        $ids = $this->referencesToIds($ruleReferences);
        $shopId = $this->getCspRuleRepository()->getById($ids[0])->getShopId();
        $this->getCommandBus()->handle(new BulkRevokeCspSourceCommand($ids, ShopConstraint::shop($shopId)));
    }

    /**
     * @Then /^I should get a CSP error that the (?:rule|log entry) was not found$/
     */
    public function assertNotFoundError(): void
    {
        $this->assertLastErrorIs(CspException::class);
    }

    /**
     * @Then /^a CSP rule for shop (\d+) with directive "([^"]*)" and source "([^"]*)" should exist$/
     */
    public function assertRuleExists(string $shopId, string $directive, string $source): void
    {
        if (null === $this->getCspRuleRepository()->findOneByShopDirectiveSource((int) $shopId, $directive, $source)) {
            throw new RuntimeException(sprintf(
                'Expected a CSP rule "%s" from "%s" for shop %d, none was found',
                $directive,
                $source,
                (int) $shopId,
            ));
        }
    }

    /**
     * @Then /^no CSP rule for shop (\d+) with directive "([^"]*)" and source "([^"]*)" should exist$/
     */
    public function assertRuleNotExists(string $shopId, string $directive, string $source): void
    {
        if (null !== $this->getCspRuleRepository()->findOneByShopDirectiveSource((int) $shopId, $directive, $source)) {
            throw new RuntimeException(sprintf(
                'Expected no CSP rule "%s" from "%s" for shop %d, but one was found',
                $directive,
                $source,
                (int) $shopId,
            ));
        }
    }

    /**
     * @Then /^shop (\d+) should have (\d+) CSP rules?$/
     */
    public function assertRuleCountForShop(string $shopId, string $expectedCount): void
    {
        $actual = count($this->getCspRuleRepository()->getRulesByShop((int) $shopId));

        if ($actual !== (int) $expectedCount) {
            throw new RuntimeException(sprintf(
                'Expected %d CSP rule(s) for shop %d, found %d',
                (int) $expectedCount,
                (int) $shopId,
                $actual,
            ));
        }
    }

    /**
     * @Then /^shop (\d+) should have no CSP rules$/
     */
    public function assertNoRulesForShop(string $shopId): void
    {
        $this->assertRuleCountForShop($shopId, '0');
    }

    /**
     * @Then /^CSP rules "([^"]*)" and "([^"]*)" should be the same rule$/
     */
    public function assertSameRule(string $firstReference, string $secondReference): void
    {
        $firstId = $this->referenceToId($firstReference);
        $secondId = $this->referenceToId($secondReference);

        if ($firstId !== $secondId) {
            throw new RuntimeException(sprintf(
                'Expected CSP rules "%s" and "%s" to be the same rule, but got #%d and #%d',
                $firstReference,
                $secondReference,
                $firstId,
                $secondId,
            ));
        }
    }

    /**
     * @Then /^the CSP rules for shop (\d+) should not include directive "([^"]*)" and source "([^"]*)"$/
     */
    public function assertRulesByShopExclude(string $shopId, string $directive, string $source): void
    {
        foreach ($this->getCspRuleRepository()->getRulesByShop((int) $shopId) as $rule) {
            if ($rule['directive'] === $directive && $rule['source'] === $source) {
                throw new RuntimeException(sprintf(
                    'Expected shop %d rules not to include "%s" from "%s", but it was returned by getRulesByShop',
                    (int) $shopId,
                    $directive,
                    $source,
                ));
            }
        }
    }

    /**
     * @Then /^the CSP grid should flag directive "([^"]*)" source "([^"]*)" for shop (\d+) as (weakening|not weakening)$/
     */
    public function assertGridWeakening(string $directive, string $source, string $shopId, string $expected): void
    {
        $queryBuilder = CommonFeatureContext::getContainer()->get('prestashop.core.grid.query_builder.csp_log');
        $filters = new CspLogFilters(ShopConstraint::shop((int) $shopId), CspLogFilters::getDefaults());

        $match = null;
        foreach ($queryBuilder->getSearchQueryBuilder($filters)->executeQuery()->fetchAllAssociative() as $row) {
            if ($row['directive'] === $directive && $row['source'] === $source) {
                $match = $row;

                break;
            }
        }

        if (null === $match) {
            throw new RuntimeException(sprintf('No grid row for "%s" from "%s" on shop %d', $directive, $source, (int) $shopId));
        }

        $isWeakening = 1 === (int) $match['is_weakening'];
        if ($isWeakening !== ('weakening' === $expected)) {
            throw new RuntimeException(sprintf(
                'Expected "%s" from "%s" to be %s, but the grid reported is_weakening=%d',
                $directive,
                $source,
                $expected,
                (int) $match['is_weakening'],
            ));
        }
    }

    /**
     * @When /^I backdate the CSP log for shop (\d+) source "([^"]*)" by (\d+) days$/
     */
    public function backdateLog(string $shopId, string $source, string $days): void
    {
        $entityManager = CommonFeatureContext::getContainer()->get('doctrine.orm.entity_manager');
        /** @var CspLog|null $log */
        $log = $entityManager->getRepository(CspLog::class)->findOneBy(['shopId' => (int) $shopId, 'source' => $source]);
        if (null === $log) {
            throw new RuntimeException(sprintf('No log row for "%s" on shop %d', $source, (int) $shopId));
        }

        $log->setDateAdd((new DateTimeImmutable())->modify(sprintf('-%d days', (int) $days)));
        $entityManager->flush();
    }

    /**
     * @When /^I prune CSP reports for shop (\d+) older than (\d+) days$/
     */
    public function pruneOlderThan(string $shopId, string $days): void
    {
        $this->getCspLogRepository()->deleteOlderThanByShop(
            (int) $shopId,
            (new DateTimeImmutable())->modify(sprintf('-%d days', (int) $days))
        );
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

    private function getCspRuleRepository(): CspRuleRepository
    {
        return CommonFeatureContext::getContainer()->get(CspRuleRepository::class);
    }
}
