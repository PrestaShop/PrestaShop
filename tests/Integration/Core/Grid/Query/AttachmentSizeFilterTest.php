<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Grid\Query;

use Db;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Grid\Query\AttachmentQueryBuilder;
use PrestaShop\PrestaShop\Core\Search\Filters\AttachmentFilters;
use PrestaShop\PrestaShop\Core\Util\File\FileSizeConverter;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The Files grid converts the stored byte count for display, so no value read off the Size column can
 * match it. The filter is a range over the stored count instead.
 */
class AttachmentSizeFilterTest extends KernelTestCase
{
    private const SIZES = [512, 45678, 3145728];

    private AttachmentQueryBuilder $queryBuilder;

    /** @var array<int, int> id_attachment keyed by size */
    private array $ids = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $languageContextBuilder = self::getContainer()->get(LanguageContextBuilder::class);
        $languageContextBuilder->setLanguageId(1);

        $this->queryBuilder = self::getContainer()->get('prestashop.core.grid.query_builder.attachment');

        foreach (self::SIZES as $size) {
            Db::getInstance()->insert('attachment', [
                'file' => sha1((string) $size . microtime()),
                'file_name' => 'size-filter-' . $size . '.bin',
                'file_size' => $size,
                'mime' => 'application/octet-stream',
            ]);
            $id = (int) Db::getInstance()->Insert_ID();
            $this->ids[$size] = $id;

            foreach (Db::getInstance()->executeS('SELECT id_lang FROM ' . _DB_PREFIX_ . 'lang') as $lang) {
                Db::getInstance()->insert('attachment_lang', [
                    'id_attachment' => $id,
                    'id_lang' => (int) $lang['id_lang'],
                    'name' => 'size filter ' . $size,
                    'description' => '',
                ]);
            }
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->ids as $id) {
            Db::getInstance()->delete('attachment_lang', 'id_attachment = ' . $id);
            Db::getInstance()->delete('attachment', 'id_attachment = ' . $id);
        }
        $this->ids = [];

        parent::tearDown();
    }

    /**
     * The reported case: a file larger than a kilobyte shows as "44.61kB" and could not be found at all.
     */
    public function testAFileAboveAKilobyteCanBeFound(): void
    {
        $converter = new FileSizeConverter();
        $this->assertSame('44.61kB', $converter->convert(45678), 'the display this filter has to serve');

        $found = $this->search(['min_field' => 45678, 'max_field' => 45678]);

        $this->assertSame([$this->ids[45678]], $found);
    }

    public function testAMinimumKeepsTheFilesAtOrAboveIt(): void
    {
        $found = $this->search(['min_field' => 45678]);

        $this->assertContains($this->ids[45678], $found);
        $this->assertContains($this->ids[3145728], $found);
        $this->assertNotContains($this->ids[512], $found);
    }

    public function testAMaximumKeepsTheFilesAtOrBelowIt(): void
    {
        $found = $this->search(['max_field' => 512]);

        $this->assertContains($this->ids[512], $found);
        $this->assertNotContains($this->ids[45678], $found);
        $this->assertNotContains($this->ids[3145728], $found);
    }

    public function testARangeKeepsWhatFallsInside(): void
    {
        $found = $this->search(['min_field' => 1024, 'max_field' => 100000]);

        $this->assertContains($this->ids[45678], $found);
        $this->assertNotContains($this->ids[512], $found);
        $this->assertNotContains($this->ids[3145728], $found);
    }

    /**
     * A filter saved before the size became a range arrives as a scalar. It cannot be honoured, and
     * reading it as one bound or the other would answer a question nobody asked.
     */
    public function testAFilterSavedBeforeTheRangeIsIgnoredRatherThanGuessed(): void
    {
        $found = $this->search('512');

        foreach ($this->ids as $id) {
            $this->assertContains($id, $found);
        }
    }

    /**
     * @param array<string, int>|string $fileSizeFilter
     *
     * @return int[]
     */
    private function search($fileSizeFilter): array
    {
        $filters = new AttachmentFilters([
            'limit' => 500,
            'offset' => 0,
            'orderBy' => 'id_attachment',
            'sortOrder' => 'asc',
            'filters' => ['file_size' => $fileSizeFilter],
        ]);

        $rows = $this->queryBuilder->getSearchQueryBuilder($filters)->executeQuery()->fetchAllAssociative();

        return array_map('intval', array_column($rows, 'id_attachment'));
    }
}
