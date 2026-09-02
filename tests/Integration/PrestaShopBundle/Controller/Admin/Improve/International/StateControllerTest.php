<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\PrestaShopBundle\Controller\Admin\Improve\International;

use PrestaShop\PrestaShop\Core\Domain\State\Command\DeleteStateCommand;
use PrestaShop\PrestaShop\Core\Domain\State\CommandHandler\DeleteStateHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\DeleteStateException;
use State;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Tests\Integration\Utility\LoginTrait;

class StateControllerTest extends WebTestCase
{
    use LoginTrait;

    /**
     * @var KernelBrowser
     */
    private $client;

    /**
     * @var Router
     */
    private $router;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = self::createClient();
        $this->loginUser($this->client);

        $this->router = self::$kernel->getContainer()->get('router');
    }

    public function testDeleteActionAddsErrorFlashWhenDeleteStateExceptionIsThrown(): void
    {
        $this->client->disableReboot();

        $deleteStateHandler = $this->createMock(DeleteStateHandlerInterface::class);
        $deleteStateHandler
            ->method('handle')
            ->willReturnCallback(function (DeleteStateCommand $command) {
                throw new DeleteStateException(
                    'Delete failed',
                    DeleteStateException::FAILED_DELETE
                );
            });

        self::$kernel->getContainer()->set(DeleteStateHandlerInterface::class, $deleteStateHandler);

        $stateId = State::getIdByIso('FL');

        $this->client->request(
            'DELETE',
            $this->router->generate('admin_states_delete', ['stateId' => $stateId])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());

        /** @var Session $session */
        $session = $this->client->getRequest()->getSession();
        $messages = $session->getFlashBag()->all();

        $this->assertArrayHasKey('error', $messages);
        $this->assertContains(
            'An error occurred while deleting the object.',
            $messages['error'],
            print_r($messages['error'], true)
        );
    }
}
