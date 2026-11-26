<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Sell\Catalog;

use Attachment;
use Configuration;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\Utility\LoginTrait;
use Tests\TestCase\SymfonyIntegrationTestCase;

final class AttachmentControllerTest extends SymfonyIntegrationTestCase
{
    use LoginTrait;

    private const NAMES = ['Att c/d 43618', 'Att a+b 43618', 'Att e&f 43618', 'Att g?h 43618', 'Att plain 43618'];

    private RouterInterface $router;

    /**
     * @var array<string, int>
     */
    private array $attachmentIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->loginUser($this->client);
        $this->router = $this->client->getContainer()->get('router');

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
     * @dataProvider getNames
     */
    public function testSearchFindsTheNameSentAsQueryParameter(string $name): void
    {
        $this->assertSame(
            [$this->attachmentIds[$name]],
            $this->search($this->router->generate('admin_attachments_search_by_query', ['query' => $name]))
        );
    }

    public static function getNames(): iterable
    {
        foreach (self::NAMES as $name) {
            yield $name => [$name];
        }
    }

    public function testSearchStillReadsThePhraseFromThePath(): void
    {
        $this->assertSame(
            [$this->attachmentIds['Att plain 43618']],
            $this->search($this->router->generate('admin_attachments_search', ['searchPhrase' => 'Att plain 43618']))
        );
    }

    public function testSearchWithoutPhraseFindsNothing(): void
    {
        $this->client->request('GET', $this->router->generate('admin_attachments_search_by_query'));

        $this->assertSame(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return int[]
     */
    private function search(string $url): array
    {
        $this->client->request('GET', $url);
        $response = $this->client->getResponse();
        $this->assertTrue($response->isSuccessful(), (string) $response->getStatusCode());

        return array_column(json_decode((string) $response->getContent(), true), 'attachment_id');
    }
}
