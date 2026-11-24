<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Attachment;

use Attachment;
use Configuration;
use PrestaShop\PrestaShop\Adapter\Attachment\AttachmentRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class AttachmentRepositoryTest extends KernelTestCase
{
    private const NAMES = ['Att 100% 43635', 'Att 1000 43635', 'Att a_b 43635', 'Att axb 43635', 'Att c\\d 43635', 'Att cd 43635'];

    private AttachmentRepository $repository;

    /**
     * @var array<string, int>
     */
    private array $attachmentIds = [];

    protected function setUp(): void
    {
        self::bootKernel();
        $this->repository = self::getContainer()->get(AttachmentRepository::class);

        foreach (self::NAMES as $name) {
            $attachment = new Attachment();
            $attachment->name = [(int) Configuration::get('PS_LANG_DEFAULT') => $name];
            $attachment->file = sha1($name);
            $attachment->file_name = 'file.txt';
            $attachment->file_size = 0;
            $attachment->mime = 'text/plain';
            $attachment->add();
            $this->attachmentIds[$name] = (int) $attachment->id;
        }
    }

    protected function tearDown(): void
    {
        foreach ($this->attachmentIds as $attachmentId) {
            (new Attachment($attachmentId))->delete();
        }
        parent::tearDown();
    }

    /**
     * @dataProvider getPhrases
     */
    public function testSearchMatchesWildcardCharactersAsTyped(string $phrase, string $expectedName): void
    {
        $this->assertSame([$this->attachmentIds[$expectedName]], $this->search($phrase));
    }

    public static function getPhrases(): iterable
    {
        yield 'percent' => ['100% 43635', 'Att 100% 43635'];
        yield 'underscore' => ['a_b 43635', 'Att a_b 43635'];
        yield 'backslash' => ['c\\d 43635', 'Att c\\d 43635'];
    }

    /**
     * @return int[]
     */
    private function search(string $phrase): array
    {
        $foundIds = array_map(
            static fn ($attachment): int => (int) $attachment['id_attachment'],
            $this->repository->search($phrase)
        );

        return array_values(array_intersect($foundIds, $this->attachmentIds));
    }
}
