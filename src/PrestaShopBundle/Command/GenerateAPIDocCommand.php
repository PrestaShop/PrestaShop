<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Command;

use ApiPlatform\Symfony\Action\DocumentationAction;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

#[AsCommand(name: 'prestashop:generate:apidoc', description: 'Generate APIDoc')]
class GenerateAPIDocCommand extends Command
{
    public function __construct(
        #[Autowire(service: 'api_platform.action.documentation')]
        protected readonly DocumentationAction $documentationAction,
    ) {
        parent::__construct('prestashop:generate:apidoc');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $request = Request::create('docs.json', Request::METHOD_GET);
        $request->attributes->add([
            '_format' => 'json',
        ]);
        /** @var Response $generatedDoc */
        $generatedDoc = $this->documentationAction->__invoke($request);
        $output->writeln($generatedDoc->getContent());

        return 0;
    }
}
