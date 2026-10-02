<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace Tests\Integration\Classes;

use Configuration;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Utility\ContextMockerTrait;
use Tools;

class ToolsTest extends TestCase
{
    use ContextMockerTrait;

    /**
     * @var mixed the value to put back into PS_REWRITING_SETTINGS, null when it was never changed
     */
    private $originalRewritingSettings;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();
    }

    protected function tearDown(): void
    {
        // WHY: the friendly-URL assertions below have to switch rewriting on, and this configuration
        // is process-global - leaving it on would flip the behaviour of every later test in the run.
        if ($this->originalRewritingSettings !== null) {
            Configuration::updateValue('PS_REWRITING_SETTINGS', $this->originalRewritingSettings);
            $this->originalRewritingSettings = null;
        }

        parent::tearDown();
    }

    /**
     * @dataProvider getUrlsToSanitize
     *
     * @param string $url
     * @param string $expected
     * @param string $physicalUri
     *
     * @return void
     */
    public function testSanitizeAdminUrl(string $url, string $expected, string $physicalUri = ''): void
    {
        // Override the mocked shop in context
        static::getContext()->shop->physical_uri = $physicalUri;
        $this->assertEquals($expected, Tools::sanitizeAdminUrl($url));
    }

    public function getUrlsToSanitize(): iterable
    {
        yield 'url starting with index.php' => [
            'index.php?controller=AdminModules',
            'http://localhost/admin-dev/index.php?controller=AdminModules',
        ];

        yield 'url starting with /admin-dev/index.php' => [
            '/admin-dev/index.php?controller=AdminModules',
            'http://localhost/admin-dev/index.php?controller=AdminModules',
        ];

        yield 'url symfony style with admin-dev' => [
            '/admin-dev/modules/link-widget/list',
            'http://localhost/admin-dev/modules/link-widget/list',
        ];

        yield 'url symfony style without admin-dev' => [
            '/modules/link-widget/list',
            'http://localhost/admin-dev/modules/link-widget/list',
        ];

        yield 'url symfony style without starting /' => [
            'modules/link-widget/list',
            'http://localhost/admin-dev/modules/link-widget/list',
        ];

        yield 'absolute legacy url' => [
            'http://localhost/admin-dev/index.php?controller=AdminModules',
            'http://localhost/admin-dev/index.php?controller=AdminModules',
        ];

        yield 'absolute symfony url' => [
            'http://localhost/admin-dev/modules/link-widget/list',
            'http://localhost/admin-dev/modules/link-widget/list',
        ];

        yield 'external url' => [
            'http://www.prestahop-project.org',
            'http://www.prestahop-project.org',
        ];

        // Now test use cases where the shop is installed in a sub folder so physical URI is not empty
        yield 'shop with physical uri, url does not contain it' => [
            '/admin-dev/modules/blockwishlist/configuration',
            'http://localhost/shop_sub_folder/admin-dev/modules/blockwishlist/configuration',
            '/shop_sub_folder',
        ];

        yield 'shop with physical uri, url does not contain it nor the admin folder' => [
            '/modules/blockwishlist/configuration',
            'http://localhost/shop_sub_folder/admin-dev/modules/blockwishlist/configuration',
            '/shop_sub_folder',
        ];

        yield 'shop with physical uri, url already contains it at the beginning' => [
            '/shop_sub_folder/admin-dev/modules/blockwishlist/configuration',
            'http://localhost/shop_sub_folder/admin-dev/modules/blockwishlist/configuration',
            // Works with trailing / also
            '/shop_sub_folder/',
        ];

        yield 'shop with physical uri, url already contains it but in the middle' => [
            '/admin-dev/modules/blockwishlist/shop_sub_folder/configuration',
            'http://localhost/shop_sub_folder/admin-dev/modules/blockwishlist/shop_sub_folder/configuration',
            '/shop_sub_folder',
        ];

        yield 'shop with physical uri, absolute url' => [
            'http://localhost/shop_sub_folder/admin-dev/modules/blockwishlist/shop_sub_folder/configuration',
            'http://localhost/shop_sub_folder/admin-dev/modules/blockwishlist/shop_sub_folder/configuration',
            '/shop_sub_folder',
        ];

        yield 'shop with physical uri, url starting with index.php' => [
            'index.php?controller=AdminModules',
            'http://localhost/shop_sub_folder/admin-dev/index.php?controller=AdminModules',
            '/shop_sub_folder',
        ];

        yield 'shop with physical uri, url starting with /admin-dev/index.php' => [
            '/admin-dev/index.php?controller=AdminModules',
            'http://localhost/shop_sub_folder/admin-dev/index.php?controller=AdminModules',
            '/shop_sub_folder',
        ];

        yield 'shop with physical uri, url starting with /shop_sub_folder/admin-dev/index.php' => [
            '/shop_sub_folder/admin-dev/index.php?controller=AdminModules',
            'http://localhost/shop_sub_folder/admin-dev/index.php?controller=AdminModules',
            '/shop_sub_folder',
        ];

        yield 'shop with physical uri, external url' => [
            'http://www.prestahop-project.org',
            'http://www.prestahop-project.org',
            '/shop_sub_folder',
        ];
    }

    /**
     * The public utility pages carry a "back" parameter, so they exist under one URL per page of the
     * shop. Excluding them with robots.txt does not keep them out of the index - it only stops a
     * crawler from reading the canonical and the noindex those pages send. So neither the pages nor
     * the parameter may be disallowed.
     *
     * @return void
     */
    /**
     * A URL that robots.txt excludes is still indexed when enough links point at it, and the page's
     * own noindex is never read while it is - so a page that sends noindex has to stay crawlable.
     *
     * @return void
     */
    public function testRobotsContentDoesNotBlockPagesThatSendNoindex(): void
    {
        $robots = $this->getRobotsContentWithFriendlyUrls();
        $friendlyUrls = $this->getDisallowedFriendlyUrls($robots);

        $this->assertNotEmpty(
            $friendlyUrls,
            'No page was blocked by its friendly URL, so the assertions below would hold vacuously.'
        );

        $noindexPages = [
            'authentication' => 'login',
            'registration' => 'registration',
            'guest-tracking' => 'guest-tracking',
        ];

        foreach ($noindexPages as $controller => $urlRewrite) {
            $this->assertNotContains(
                $urlRewrite,
                $friendlyUrls,
                sprintf('The "%s" page must stay crawlable so that its noindex is read.', $controller)
            );
            $this->assertNotContains(
                'controller=' . $controller,
                $robots['GB'],
                sprintf('The "%s" page must stay crawlable so that its noindex is read.', $controller)
            );
        }

        foreach (['?back=', '&back='] as $parameter) {
            $this->assertNotContains(
                $parameter,
                $robots['GB'],
                'Blocking the "back" parameter hides the noindex of every page reached through it.'
            );
        }
    }

    /**
     * The pages that stay blocked are the ones behind a login, the ones that are not pages at all,
     * and password recovery - whose controller acts on GET parameters. This asserts the change
     * above narrowed the list rather than emptying it.
     *
     * @return void
     */
    public function testRobotsContentStillBlocksPrivateAndNonPageControllers(): void
    {
        $robots = $this->getRobotsContentWithFriendlyUrls();
        $friendlyUrls = $this->getDisallowedFriendlyUrls($robots);

        $blockedPages = [
            'my-account' => 'my-account',
            'identity' => 'identity',
            'order' => 'order',
            'password' => 'password-recovery',
        ];

        foreach ($blockedPages as $controller => $urlRewrite) {
            $this->assertContains($urlRewrite, $friendlyUrls);
            $this->assertContains('controller=' . $controller, $robots['GB']);
        }

        foreach (['pdf-invoice', 'get-file', 'cart'] as $controller) {
            $this->assertContains('controller=' . $controller, $robots['GB']);
        }

        foreach (['?q=', '&q=', '?order=', '&order='] as $parameter) {
            $this->assertContains($parameter, $robots['GB']);
        }
    }

    /**
     * The friendly-URL half of the file is where the "Disallow: /login" line comes from, and it is
     * only generated when rewriting is on, so both tests above need it enabled to mean anything.
     *
     * @return array
     */
    private function getRobotsContentWithFriendlyUrls(): array
    {
        $this->originalRewritingSettings = Configuration::get('PS_REWRITING_SETTINGS');
        Configuration::updateValue('PS_REWRITING_SETTINGS', 1);

        return Tools::getRobotsContent();
    }

    /**
     * @param array $robots
     *
     * @return array the blocked friendly URLs of every active language, flattened
     */
    private function getDisallowedFriendlyUrls(array $robots): array
    {
        return array_merge([], ...array_values($robots['Files']));
    }
}
