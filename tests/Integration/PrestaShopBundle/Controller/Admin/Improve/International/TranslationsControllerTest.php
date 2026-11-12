<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Improve\International;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Field\FileFormField;
use Tests\Integration\Utility\LoginTrait;
use ZipArchive;

class TranslationsControllerTest extends WebTestCase
{
    use LoginTrait;

    protected KernelBrowser $client;
    protected Router $router;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
        // The kernel must survive between the GET that renders the form and the POST that submits
        // it, or the session behind the CSRF token is gone.
        $this->client->disableReboot();
        $this->loginUser($this->client);
        $this->router = self::$kernel->getContainer()->get('router');
    }

    /**
     * The import block is the only file field on this page, so it is also the only one that needs
     * the form to be multipart. Rendering it is what proves the field, the form theme and the
     * enctype agree.
     */
    public function testTheSettingsPageOffersAnImportForm(): void
    {
        $crawler = $this->client->request('GET', $this->router->generate('admin_international_translations_show_settings'));

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());

        // Every form on this page is built by the same handler and so is named "form"; the pack field
        // is what tells this one apart.
        $fileInput = $crawler->filter('input[type="file"][name="form[pack]"]');
        $this->assertCount(1, $fileInput, 'The language pack file field was not rendered.');

        // The action carries a CSRF token, so match on the path rather than the end of the attribute.
        $form = $crawler->filter('form[action*="/import-language-pack"]');
        $this->assertCount(1, $form, 'The import form does not post to the import route.');
        $this->assertSame(
            'multipart/form-data',
            $form->attr('enctype'),
            'The import form cannot carry a file without a multipart enctype.'
        );
    }

    /**
     * Submitting covers the part the other tests cannot reach: the multipart request, the file
     * constraint, and the uploaded file arriving as one. A pack that will be refused is used on
     * purpose, so the request stops at validation and nothing is extracted and no cache is cleared.
     */
    public function testSubmittingAPackThatIsNotOneReportsItAndInstallsNothing(): void
    {
        $archivePath = tempnam(sys_get_temp_dir(), 'pack') . '.zip';
        $archive = new ZipArchive();
        $archive->open($archivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $archive->addFromString('fr-FR/AdminActions.fr-FR.xlf', '<xliff/>');
        $archive->addFromString('fr-FR/shell.php', '<?php echo 1;');
        $archive->close();

        $crawler = $this->client->request('GET', $this->router->generate('admin_international_translations_show_settings'));
        $form = $crawler->filter('form[action*="/import-language-pack"]')->form();

        $packField = $form['form[pack]'];
        $this->assertInstanceOf(FileFormField::class, $packField);
        $packField->upload($archivePath);
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $crawler = $this->client->followRedirect();

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        $this->assertStringContainsString(
            'not a translation pack',
            $crawler->filter('.alert-danger')->text(),
            'The refusal was not reported to the merchant.'
        );
        $this->assertFileDoesNotExist(_PS_ROOT_DIR_ . '/translations/fr-FR/shell.php');

        @unlink($archivePath);
    }

    /**
     * The block sits beside the export one it mirrors, and the page still renders every other form.
     */
    public function testTheSettingsPageStillOffersTheFormsItAlreadyHad(): void
    {
        $crawler = $this->client->request('GET', $this->router->generate('admin_international_translations_show_settings'));

        $this->assertSame(200, $this->client->getResponse()->getStatusCode());
        foreach (['/export', '/copy', '/add-update-language', '/modify'] as $action) {
            $this->assertCount(
                1,
                $crawler->filter(sprintf('form[action*="%s?"]', $action)),
                sprintf('The form posting to %s disappeared from the page.', $action)
            );
        }
    }
}
