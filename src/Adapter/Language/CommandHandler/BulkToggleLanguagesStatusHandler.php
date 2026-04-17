<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Language\CommandHandler;

use PrestaShop\PrestaShop\Adapter\File\RobotsTextFileGenerator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\BulkToggleLanguagesStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\CommandHandler\BulkToggleLanguagesStatusHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;

/**
 * Toggles multiple languages status using legacy Language object model
 *
 * @internal
 */
#[AsCommandHandler]
final class BulkToggleLanguagesStatusHandler extends AbstractLanguageHandler implements BulkToggleLanguagesStatusHandlerInterface
{
    /**
     * @var RobotsTextFileGenerator
     */
    private $robotsTextFileGenerator;

    /**
     * @param RobotsTextFileGenerator $robotsTextFileGenerator
     */
    public function __construct(RobotsTextFileGenerator $robotsTextFileGenerator)
    {
        $this->robotsTextFileGenerator = $robotsTextFileGenerator;
    }

    /**
     * @param BulkToggleLanguagesStatusCommand $command
     */
    public function handle(BulkToggleLanguagesStatusCommand $command)
    {
        foreach ($command->getLanguageIds() as $languageId) {
            $language = $this->getLegacyLanguageObject($languageId);

            $this->assertLanguageIsNotDefault($language, $command);

            $language->active = $command->getStatus();

            if (false === $language->update()) {
                throw new LanguageException(sprintf('Failed to toggle language "%s" to status %s', $language->id, var_export($command->getStatus(), true)));
            }
        }
        $this->robotsTextFileGenerator->generateFile();
    }
}
