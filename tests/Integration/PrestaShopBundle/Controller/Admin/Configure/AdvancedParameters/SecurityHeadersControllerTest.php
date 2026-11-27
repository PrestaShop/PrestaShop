<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Configure\AdvancedParameters;

use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use PrestaShopBundle\Entity\FeatureFlag;
use Tests\Integration\Utility\LoginTrait;
use Tests\Resources\DatabaseDump;
use Tests\TestCase\SymfonyIntegrationTestCase;

/**
 * HTTP-path coverage for the static "Security headers" page (its own tab, separate from the CSP tab).
 */
class SecurityHeadersControllerTest extends SymfonyIntegrationTestCase
{
    use LoginTrait;

    private $router;

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['feature_flag']);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->loginUser($this->client);
        $this->router = $this->client->getContainer()->get('router');

        // The page is gated behind the 'csp' feature flag, which ships disabled.
        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        /** @var FeatureFlag|null $flag */
        $flag = $entityManager->getRepository(FeatureFlag::class)->findOneBy(['name' => FeatureFlagSettings::FEATURE_FLAG_CSP]);
        $flag->enable();
        $entityManager->flush();
    }

    public function testThePageRendersTheStaticHeadersForm(): void
    {
        $crawler = $this->client->request('GET', $this->router->generate('admin_security_headers_index'));

        $this->assertResponseIsSuccessful();
        $this->assertSame(
            1,
            $crawler->filter('#configuration_fieldset_security_headers')->count(),
            'The Security headers page must render the static-headers settings form'
        );
        // The CSP curation grid belongs to the separate Content Security Policy tab, not this page.
        $this->assertSame(0, $crawler->filter('#csp_log_grid_table')->count());
    }

    public function testSavingTheFormRedirectsBackToThePage(): void
    {
        $this->client->request('POST', $this->router->generate('admin_security_headers_save'), [
            'security_headers_block' => [
                'nosniff' => '1',
                'frame_options' => 'SAMEORIGIN',
                'referrer_policy' => 'strict-origin-when-cross-origin',
                'hsts' => '0',
                'hsts_max_age' => '15552000',
                'hsts_subdomains' => '0',
                'hsts_preload' => '0',
                'permissions_policy' => '',
            ],
        ]);

        $this->assertResponseRedirects();
        $this->assertStringContainsString(
            $this->router->generate('admin_security_headers_index'),
            (string) $this->client->getResponse()->headers->get('Location')
        );
    }
}
