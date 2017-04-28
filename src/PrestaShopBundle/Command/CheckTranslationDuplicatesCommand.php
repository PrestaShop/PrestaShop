<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class CheckTranslationDuplicatesCommand extends ContainerAwareCommand
{

    protected function configure()
    {
        $this
            ->setName('translation:find-duplicates')
            ->setDescription('Find duplicates of your translations');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $translator = $this->getContainer()->get('translator');
        $catalogue = $translator->getCatalogue()->all();

        $progress = new ProgressBar($output, count($catalogue, true));
        $progress->start();
        $progress->setRedrawFrequency(20);

        $duplicates = array();

        foreach ($catalogue as $domain => $messages) {
            $nbOfMessages = count($messages);
            $messages = array_keys($messages);
            
            for ($i = 0 ; $i < $nbOfMessages ; ++$i) {
                for ($j = ($i +1) ; $j < $nbOfMessages ; ++$j) {
                    if ($this->check($messages[$i], $messages[$j])) {
                        $duplicates[$domain][] = array($i => $messages[$i], $j => $messages[$j]);
                    }
                }
                $progress->advance();
            }
        }

        $progress->finish();
        $output->writeln('');

        if (count($duplicates)) {
            $output->writeln('Duplicates found:');
            dump($duplicates);
        }else {
            $output->writeln('Awww yisss ! There is no duplicate in your translator catalogue.');
        }
    }

    protected function check($message1, $message2)
    {
        return $this->removeParams($message1) == $this->removeParams($message2);
    }

    protected function removeParams($message)
    {
        // Remove PrestaShop arguments %<arg>%
        $message = preg_replace('/%\w+%/', '~', $message);
        // Remove all related sprintf arguments
        $message = preg_replace('#(?:%%|%(?:[0-9]+\$)?[+-]?(?:[ 0]|\'.)?-?[0-9]*(?:\.[0-9]+)?[bcdeufFosxX])#', '~', $message);

        return $message;
    }
}
