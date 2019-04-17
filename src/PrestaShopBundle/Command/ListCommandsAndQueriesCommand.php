<?php
/**
 * 2007-2019 PrestaShop and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Command;

use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ListCommandsAndQueriesCommand extends ContainerAwareCommand
{
    /**
     * @inheritdoc
     */
    public function configure()
    {
        $this
            ->setName('prestashop:cqrs:available-commands')
            ->setDescription('Lists available CQRS commands and queries')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $io = new SymfonyStyle($input, $output);
        $rows = [];

        foreach ($this->getCommands() as $file) {
            $fileName = $file->getBasename();
            $className = str_replace('.php', '', $fileName);
            $ns = $this->extractNamespace($file);
            $fullClassName = $ns . '\\' . $className;
            $class =  new ReflectionClass($fullClassName);

            $docBlock = preg_replace('/[\*\/]/', '', $class->getDocComment());

            $column[] = $fullClassName;
            $rows[] = [
                $fullClassName,
                $docBlock
            ];
        }
        $io->table(['class', 'description'], $rows);

    }

    /**
     * @return Finder
     */
    private function getCommands()
    {
        return Finder::create()->files()->in(['./src/Core/Domain/*/Command', './src/Core/Domain/*/Query']);
    }

    private function extractNamespace(SplFileInfo $file)
    {
        $src = file_get_contents($file);
        $tokens = token_get_all($src);
        $count = count($tokens);
        $i = 0;
        $namespace = '';
        $namespace_ok = false;
        while ($i < $count) {
            $token = $tokens[$i];
            if (is_array($token) && $token[0] === T_NAMESPACE) {
                // Found namespace declaration
                while (++$i < $count) {
                    if ($tokens[$i] === ';') {
                        $namespace_ok = true;
                        $namespace = trim($namespace);
                        break;
                    }
                    $namespace .= is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i];
                }
                break;
            }
            $i++;
        }
        if ($namespace_ok) {
            return $namespace;
        }

        return null;
    }
}
