<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
