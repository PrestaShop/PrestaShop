<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Improve\Design;

use CMSCategory;
use Configuration;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\Utility\LoginTrait;
use Tests\Resources\DatabaseDump;
use Tests\TestCase\SymfonyIntegrationTestCase;

class CmsPageControllerTest extends SymfonyIntegrationTestCase
{
    use LoginTrait;

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(['admin_filter', 'cms_category', 'cms_category_lang', 'cms_category_shop']);
    }

    public function testBackToListClimbsOneLevel(): void
    {
        $this->loginUser($this->client);
        $parentId = $this->addCategory(1, 'Back to list parent');
        $childId = $this->addCategory($parentId, 'Back to list child');

        // From a second-level category, back to its parent
        $this->assertBackToListLinksTo(['id_cms_category' => $childId], ['id_cms_category' => (string) $parentId]);
        // From a first-level category, back to the root listing
        $this->assertBackToListLinksTo(['id_cms_category' => $parentId], []);
    }

    private function assertBackToListLinksTo(array $listingParameters, array $expectedParameters): void
    {
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');
        $crawler = $this->client->request('GET', $router->generate('admin_cms_pages_index', $listingParameters));
        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        $link = parse_url($crawler->filter('.back-to-list-link')->attr('href'));
        parse_str($link['query'] ?? '', $parameters);
        unset($parameters['_token']);

        $this->assertSame(parse_url($router->generate('admin_cms_pages_index'), PHP_URL_PATH), $link['path']);
        $this->assertSame($expectedParameters, $parameters);
    }

    private function addCategory(int $parentId, string $name): int
    {
        $languageId = (int) Configuration::get('PS_LANG_DEFAULT');
        $category = new CMSCategory();
        $category->id_parent = $parentId;
        $category->active = true;
        $category->name = [$languageId => $name];
        $category->link_rewrite = [$languageId => str_replace(' ', '-', strtolower($name))];
        $category->add();

        return (int) $category->id;
    }
}
