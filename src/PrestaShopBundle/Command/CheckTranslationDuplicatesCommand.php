<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Command;

use PrestaShopBundle\Translation\Translator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Translation\TranslatorBagInterface;

class CheckTranslationDuplicatesCommand extends Command
{
    /**
     * @var TranslatorBagInterface
     */
    private $translator;

    public function __construct(TranslatorBagInterface $translator)
    {
        parent::__construct();
        $this->translator = $translator;
    }

    protected function configure()
    {
        $this
            ->setName('prestashop:translation:find-duplicates')
            ->setDescription('Find duplicates of your translations');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Get dependancies
        $catalogue = $this->translator->getCatalogue()->all();

        // Init progress bar
        $progress = new ProgressBar($output, count($catalogue, COUNT_RECURSIVE));
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

            return count($duplicates, COUNT_RECURSIVE);
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
        $message = preg_replace(Translator::$regexClassicParams, '~', $message);
        // Remove all related sprintf arguments
        $message = preg_replace(Translator::$regexSprintfParams, '~', $message);

        return $message;
    }
}
