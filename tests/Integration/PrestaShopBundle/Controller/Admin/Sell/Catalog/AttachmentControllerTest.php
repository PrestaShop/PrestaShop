<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Sell\Catalog;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Context\LanguageContextBuilder;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\AddAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\Command\DeleteAttachmentCommand;
use PrestaShop\PrestaShop\Core\Domain\Attachment\ValueObject\AttachmentId;
use PrestaShopBundle\Form\Admin\Sell\Product\Options\ProductAttachmentsType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\Utility\LoginTrait;
use Tests\TestCase\SymfonyIntegrationTestCase;

final class AttachmentControllerTest extends SymfonyIntegrationTestCase
{
    use LoginTrait;

    private const NAMES = ['Att c/d 43618', 'Att a+b 43618', 'Att e&f 43618', 'Att g?h 43618', 'Att plain 43618'];

    private CommandBusInterface $commandBus;

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
        $this->commandBus = self::getContainer()->get('prestashop.core.command_bus');
        $defaultLanguageId = (int) self::getContainer()->get(ConfigurationInterface::class)->get('PS_LANG_DEFAULT');
        // Outside a request nothing builds the language context, which the command checks the default name against
        /** @var LanguageContextBuilder $languageContextBuilder */
        $languageContextBuilder = self::getContainer()->get('test_language_context_builder');
        $languageContextBuilder->setDefaultLanguageId($defaultLanguageId);

        $file = (string) tempnam(sys_get_temp_dir(), 'att');
        foreach (self::NAMES as $name) {
            $command = new AddAttachmentCommand([$defaultLanguageId => $name], []);
            $command->setFileInformation($file, 0, 'text/plain', 'file.txt');
            /** @var AttachmentId $attachmentId */
            $attachmentId = $this->commandBus->handle($command);
            $this->attachmentIds[$name] = $attachmentId->getValue();
        }
        unlink($file);
    }

    protected function tearDown(): void
    {
        foreach ($this->attachmentIds as $attachmentId) {
            $this->commandBus->handle(new DeleteAttachmentCommand($attachmentId));
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

    /**
     * @dataProvider getNames
     */
    public function testSearchFindsTheNameSentByTheProductForm(string $name): void
    {
        $remoteUrl = self::getContainer()->get('form.factory')->create(ProductAttachmentsType::class)
            ->get('attached_files')->getConfig()->getOption('remote_url');

        // entity-search-input.ts puts the phrase in place of __QUERY__ with encodeURIComponent
        $this->assertSame(
            [$this->attachmentIds[$name]],
            $this->search(str_replace('__QUERY__', rawurlencode($name), $remoteUrl))
        );
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
