<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Sell\Catalog;

use Db;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\Utility\LoginTrait;
use Tests\TestCase\SymfonyIntegrationTestCase;

/**
 * The Products grid shows a Status column and the importer reads the field back as "Active (0/1)", so a
 * catalogue exported to be edited and imported again has to carry it.
 */
class ProductExportTest extends SymfonyIntegrationTestCase
{
    use LoginTrait;

    private RouterInterface $router;

    /** @var array<int, string> active flag keyed by id_product */
    private array $originalStatus = [];

    private int $enabledProduct = 0;

    private int $disabledProduct = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loginUser($this->client);
        $this->router = $this->client->getContainer()->get('router');

        $products = Db::getInstance()->executeS(
            'SELECT p.id_product, ps.active
             FROM ' . _DB_PREFIX_ . 'product p
             INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product = p.id_product AND ps.id_shop = 1
             ORDER BY p.id_product ASC
             LIMIT 2'
        );
        $this->assertCount(2, $products, 'two products are needed to tell the two statuses apart');

        foreach ($products as $product) {
            $this->originalStatus[(int) $product['id_product']] = (string) $product['active'];
        }

        [$first, $second] = array_keys($this->originalStatus);
        $this->enabledProduct = $first;
        $this->disabledProduct = $second;

        $this->setStatus($this->enabledProduct, 1);
        $this->setStatus($this->disabledProduct, 0);
    }

    protected function tearDown(): void
    {
        foreach ($this->originalStatus as $idProduct => $active) {
            $this->setStatus($idProduct, (int) $active);
        }
        $this->originalStatus = [];

        parent::tearDown();
    }

    public function testTheExportCarriesEachProductsRealStatus(): void
    {
        $rows = $this->exportedRows();

        $this->assertArrayHasKey($this->enabledProduct, $rows);
        $this->assertArrayHasKey($this->disabledProduct, $rows);

        // The status is the last column, and it is the stored flag rather than anything derived for display.
        $this->assertSame('1', end($rows[$this->enabledProduct]));
        $this->assertSame('0', end($rows[$this->disabledProduct]));
    }

    /**
     * @return array<int, string[]> the exported columns keyed by id_product
     */
    private function exportedRows(): array
    {
        $this->client->request('GET', $this->router->generate('admin_products_export'));

        // The export is a StreamedResponse: the browser kit has already drained it, so the content is on
        // the internal response and getResponse()->getContent() is empty.
        $csv = (string) $this->client->getInternalResponse()->getContent();

        $rows = [];
        foreach (explode("\n", trim($csv)) as $line) {
            $columns = str_getcsv(trim($line), ';');
            if (isset($columns[0]) && ctype_digit((string) $columns[0])) {
                $rows[(int) $columns[0]] = $columns;
            }
        }

        return $rows;
    }

    private function setStatus(int $idProduct, int $active): void
    {
        Db::getInstance()->update('product', ['active' => $active], 'id_product = ' . $idProduct);
        Db::getInstance()->update('product_shop', ['active' => $active], 'id_product = ' . $idProduct . ' AND id_shop = 1');
    }
}
