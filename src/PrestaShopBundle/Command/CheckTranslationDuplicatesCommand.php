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

use PrestaShopBundle\Translation\PrestaShopTranslatorTrait;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckTranslationDuplicatesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('prestashop:translation:find-duplicates')
            ->setDescription('Find duplicates of your translations');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Get dependancies
        $translator = $this->getContainer()->get('translator');
        $catalogue = $translator->getCatalogue()->all();

        // Init progress bar
        $progress = new ProgressBar($output, count($catalogue, true));
        $progress->start();
        $progress->setRedrawFrequency(20);

        $duplicates = [];

        foreach ($catalogue as $domain => $messages) {
            $nbOfMessages = count($messages);
            // In order to use a for() loop, we need integers as keys
            $messages = array_keys($messages);

            // We compare strings from the same array, so we have two for() loops
            for ($i = 0; $i < $nbOfMessages; ++$i) {
                for ($j = ($i + 1); $j < $nbOfMessages; ++$j) {
                    if ($this->check($messages[$i], $messages[$j])) {
                        $duplicates[$domain][] = [$i => $messages[$i], $j => $messages[$j]];
                    }
                }
                $progress->advance();
            }
        }

        $progress->finish();
        $output->writeln('');

        // If we have duplicates to fix, let's display them and return their count.
        // This will allow us to add the command in the tests.
        if (count($duplicates)) {
            $output->writeln('Duplicates found:');
            dump($duplicates);

            return count($duplicates, true);
        }

        $output->writeln('Awww yisss! There is no duplicate in your translator catalog.');

        return 0;
    }

    /**
     * We consider strings as equals if they have the same value after params cleanup.
     *
     * @param string $message1
     * @param string $message2
     *
     * @return bool
     */
    protected function check($message1, $message2)
    {
        return $this->removeParams($message1) == $this->removeParams($message2);
    }

    /**
     * This function replaces all parameters with a ~ in a string to translate.
     * This allow the algorithm to check if the strings are the same once the parameters made generic
     * i.e: Error when disabling module %module% ==> Error when disabling module ~.
     *
     * @param string $message
     *
     * @return string with replaced parameters
     */
    protected function removeParams($message)
    {
        // Remove PrestaShop arguments %<arg>%
        $message = preg_replace(PrestaShopTranslatorTrait::$regexClassicParams, '~', $message);
        // Remove all related sprintf arguments
        $message = preg_replace(PrestaShopTranslatorTrait::$regexSprintfParams, '~', $message);

        return $message;
    }
}
