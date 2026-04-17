<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Language\CommandHandler;

use PrestaShop\PrestaShop\Adapter\Employee\EmployeeLanguageUpdater;
use PrestaShop\PrestaShop\Adapter\File\RobotsTextFileGenerator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\DeleteLanguageCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\CommandHandler\DeleteLanguageHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\DefaultLanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;
use Shop;

/**
 * Deletes language using legacy object model
 *
 * @internal
 */
#[AsCommandHandler]
final class DeleteLanguageHandler extends AbstractLanguageHandler implements DeleteLanguageHandlerInterface
{
    /**
     * @var RobotsTextFileGenerator
     */
    private $robotsTextFileGenerator;

    /**
     * @var EmployeeLanguageUpdater
     */
    private $employeeLanguageUpdater;

    /**
     * @param RobotsTextFileGenerator $robotsTextFileGenerator
     */
    public function __construct(RobotsTextFileGenerator $robotsTextFileGenerator, EmployeeLanguageUpdater $employeeLanguageUpdater)
    {
        $this->robotsTextFileGenerator = $robotsTextFileGenerator;
        $this->employeeLanguageUpdater = $employeeLanguageUpdater;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(DeleteLanguageCommand $command)
    {
        $language = $this->getLegacyLanguageObject($command->getLanguageId());

        try {
            $this->assertLanguageIsNotDefault($language);
        } catch (DefaultLanguageException) {
            throw new DefaultLanguageException(
                sprintf(
                    'Default language "%s" cannot be deleted',
                    $language->iso_code
                ),
                DefaultLanguageException::CANNOT_DELETE_DEFAULT_ERROR
            );
        }

        $this->assertLanguageIsNotInUse($language);

        // language must be deleted in "ALL SHOPS" context
        Shop::setContext(Shop::CONTEXT_ALL);

        if (false === $language->delete()) {
            throw new LanguageException(sprintf('Failed to delete language "%s"', $language->iso_code));
        }

        $this->robotsTextFileGenerator->generateFile();

        $this->employeeLanguageUpdater->replaceDeletedLanguage($language->id);
    }
}
