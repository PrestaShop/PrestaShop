<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Improve\Design;

use Symfony\Component\DomCrawler\Field\FileFormField;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Tests\Integration\Utility\LoginTrait;
use Tests\TestCase\SymfonyIntegrationTestCase;

class ThemeControllerTest extends SymfonyIntegrationTestCase
{
    use LoginTrait;

    public function testImportFileThatIsNotZipArchiveShowsFormatError(): void
    {
        $this->client->disableReboot();
        $this->loginUser($this->client);
        /** @var RouterInterface $router */
        $router = $this->client->getContainer()->get('router');
        $importUrl = $router->generate('admin_themes_import');

        // Random bytes are guessed as application/octet-stream, which the form accepts
        $notZipFile = tempnam(sys_get_temp_dir(), 'ps-theme-import-');
        file_put_contents($notZipFile, random_bytes(1024));

        try {
            $crawler = $this->client->request('GET', $importUrl);
            $form = $crawler->filter('[data-role="computer-import-button"]')->form();
            /** @var FileFormField $fileField */
            $fileField = $form['import_theme[import_from_computer]'];
            $fileField->upload($notZipFile);
            $this->client->submit($form);
        } finally {
            @unlink($notZipFile);
        }

        $this->assertResponseRedirects($importUrl);

        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $messages = $session->getFlashBag()->all();
        $this->assertArrayHasKey('error', $messages);
        $this->assertContains('Invalid file format.', $messages['error'], print_r($messages['error'], true));
    }
}
