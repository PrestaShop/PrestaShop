<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\Domain\Csp\Command\AddCspRuleCommand;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspContext;
use PrestaShop\PrestaShop\Core\Domain\Csp\ValueObject\CspRuleId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShopBundle\Entity\FeatureFlag;
use Symfony\Component\DomCrawler\Crawler;
use Tests\Integration\PrestaShopBundle\Controller\GridControllerTestCase;
use Tests\Integration\PrestaShopBundle\Controller\TestEntityDTO;
use Tests\Resources\DatabaseDump;

/**
 * HTTP-path coverage for the CSP curation grid controller. The Behat suite drives the command bus
 * directly, so it cannot catch a mismatch between the field name the grid's bulk checkboxes submit
 * and the field name the controller reads — exactly the class of bug that shipped once
 * (csp_log_bulk vs csp_log_bulk_action). This test posts the bulk-revoke form using the name read
 * straight out of the rendered grid, so grid and controller are verified against each other.
 */
class SecurityHeadersControllerTest extends GridControllerTestCase
{
    private const SHOP_ID = 1;
    private const DIRECTIVE = 'script-src';
    private const SOURCE = 'https://seeded-bulk.example.com';

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['csp_log', 'csp_rule', 'feature_flag']);
    }

    public function setUp(): void
    {
        parent::setUp();

        $container = $this->client->getContainer();

        // The page is gated behind the 'csp' feature flag, which ships disabled.
        $entityManager = $container->get('doctrine.orm.entity_manager');
        /** @var FeatureFlag|null $flag */
        $flag = $entityManager->getRepository(FeatureFlag::class)->findOneBy(['name' => FeatureFlagSettings::FEATURE_FLAG_CSP]);
        $flag->enable();
        $entityManager->flush();
    }

    public function testBulkRevokeConsumesTheFieldNameTheGridSubmits(): void
    {
        // Seed one curated rule for the current shop; AddCspRuleHandler also inserts the matching
        // csp_log placeholder, so the row is visible and revocable in the log-driven grid.
        /** @var CspRuleId $ruleId */
        $ruleId = $this->client->getContainer()->get('prestashop.core.command_bus')->handle(
            new AddCspRuleCommand(self::DIRECTIVE, self::SOURCE, ShopConstraint::shop(self::SHOP_ID))
        );
        $ruleId = $ruleId->getValue();

        $crawler = $this->client->request('GET', $this->generateGridUrl());
        $this->assertResponseIsSuccessful();

        // The seeded rule is allowed, so its bulk checkbox is rendered (an un-allowed row's checkbox is
        // hidden by the disabled_field). Read the field name and the row value straight from the DOM.
        $checkbox = $crawler->filter('.js-bulk-action-checkbox')->reduce(
            fn (Crawler $node) => (int) $node->attr('value') === $ruleId
        );
        $this->assertSame(1, $checkbox->count(), 'The seeded allowed rule must render exactly one bulk checkbox');

        $submittedName = (string) $checkbox->attr('name');
        // The grid composes the field as "{gridId}_{bulkColumnId}[]"; assert the exact contract so a
        // change on either side is caught here, not silently in production.
        $this->assertSame('csp_log_bulk_action[]', $submittedName);
        $fieldName = substr($submittedName, 0, -2); // strip the trailing "[]"

        $this->client->request(
            'POST',
            $this->router->generate('admin_security_headers_bulk_revoke'),
            [$fieldName => [$ruleId]]
        );
        $this->assertResponseRedirects();

        $this->assertRuleCount(0, 'Bulk revoke posted with the grid\'s own field name must delete the rule');
    }

    public function testAdminBulkRevokeRunsOnTheBackOfficeSurface(): void
    {
        // Seed one curated rule on the ADMIN surface (stored at shop id 0, context=admin).
        /** @var CspRuleId $ruleId */
        $ruleId = $this->client->getContainer()->get('prestashop.core.command_bus')->handle(
            new AddCspRuleCommand(self::DIRECTIVE, self::SOURCE, ShopConstraint::shop(self::SHOP_ID), CspContext::ADMIN)
        );
        $ruleId = $ruleId->getValue();

        // The seeded admin rule must render a bulk checkbox on the admin grid, and the bulk action must
        // target the context-carrying admin route; the bulk modal posts to a bare route with no query
        // string, so without it the revoke would resolve against the storefront surface and silently fail.
        $crawler = $this->client->request('GET', $this->generateGridUrl(['context' => 'admin']));
        $this->assertResponseIsSuccessful();

        $checkbox = $crawler->filter('.js-bulk-action-checkbox')->reduce(
            fn (Crawler $node) => (int) $node->attr('value') === $ruleId
        );
        $this->assertSame(1, $checkbox->count(), 'The seeded admin rule must render a bulk checkbox on the admin grid');
        $this->assertStringContainsString(
            $this->router->generate('admin_security_headers_bulk_revoke_admin'),
            (string) $this->client->getResponse()->getContent(),
            'The admin grid bulk action must target the admin-context route'
        );

        $this->client->request('POST', $this->router->generate('admin_security_headers_bulk_revoke_admin'), ['csp_log_bulk_action' => [$ruleId]]);
        $this->assertResponseRedirects();

        $this->assertAdminRuleCount(0, 'Admin bulk revoke must delete the admin rule, not run on the storefront surface');
    }

    public function testAdminGridFilterSearchKeepsTheBackOfficeSurface(): void
    {
        // The grid filter form has no explicit action, so it posts to the current URL (which carries
        // ?context=admin). The search redirect must keep that param, or filtering the back-office log
        // bounces the merchant to the storefront tab.
        $this->client->request(
            'POST',
            $this->router->generate('admin_security_headers_search', ['context' => 'admin']),
            ['csp_log' => ['directive' => 'script-src']]
        );

        $this->assertResponseRedirects();
        $this->assertStringContainsString(
            'context=admin',
            (string) $this->client->getResponse()->headers->get('Location'),
            'Filtering the admin grid must redirect back to the back-office surface'
        );
    }

    public function testRevokingAnInvalidIdShowsTheNotFoundMessageNotTheDuplicateMessage(): void
    {
        // id 0 fails CspRuleId validation (INVALID_ID). Before the code-keyed error map this showed
        // the DUPLICATE_RULE "already allowed" message; it must now show the not-found message.
        $this->client->request('GET', $this->router->generate('admin_security_headers_revoke', ['cspRuleId' => 0]));
        $this->assertResponseRedirects();

        $this->client->followRedirect();
        $content = (string) $this->client->getResponse()->getContent();

        $this->assertStringContainsString('cannot be loaded', $content);
        $this->assertStringNotContainsString('already allowed', $content);
    }

    private function assertRuleCount(int $expected, string $message): void
    {
        $connection = $this->client->getContainer()->get('doctrine.dbal.default_connection');
        $prefix = $this->client->getContainer()->getParameter('database_prefix');

        $count = (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM ' . $prefix . 'csp_rule WHERE id_shop = :shop AND directive = :directive AND source = :source',
            ['shop' => self::SHOP_ID, 'directive' => self::DIRECTIVE, 'source' => self::SOURCE]
        );

        $this->assertSame($expected, $count, $message);
    }

    private function assertAdminRuleCount(int $expected, string $message): void
    {
        $connection = $this->client->getContainer()->get('doctrine.dbal.default_connection');
        $prefix = $this->client->getContainer()->getParameter('database_prefix');

        $count = (int) $connection->fetchOne(
            'SELECT COUNT(*) FROM ' . $prefix . "csp_rule WHERE id_shop = 0 AND context = 'admin' AND directive = :directive AND source = :source",
            ['directive' => self::DIRECTIVE, 'source' => self::SOURCE]
        );

        $this->assertSame($expected, $count, $message);
    }

    protected function generateGridUrl(array $routeParams = []): string
    {
        return $this->router->generate('admin_security_headers_index', $routeParams);
    }

    protected function getGridSelector(): string
    {
        return '#csp_log_grid_table';
    }

    protected function getFilterSearchButtonSelector(): string
    {
        return 'csp_log[actions][search]';
    }

    protected function parseEntityFromRow(Crawler $tr, int $i): TestEntityDTO
    {
        return new TestEntityDTO($i, []);
    }
}
