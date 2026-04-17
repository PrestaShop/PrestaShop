<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Language\CommandHandler;

use PrestaShop\PrestaShop\Adapter\File\RobotsTextFileGenerator;
use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Language\Command\BulkDeleteLanguagesCommand;
use PrestaShop\PrestaShop\Core\Domain\Language\CommandHandler\BulkDeleteLanguagesHandlerInterface;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\DefaultLanguageException;
use PrestaShop\PrestaShop\Core\Domain\Language\Exception\LanguageException;
use Shop;

/**
 * Deletes languages using legacy Language object model
 *
 * @internal
 */
#[AsCommandHandler]
final class BulkDeleteLanguagesHandler extends AbstractLanguageHandler implements BulkDeleteLanguagesHandlerInterface
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
     * {@inheritdoc}
     */
    public function handle(BulkDeleteLanguagesCommand $command)
    {
        // language can only be modified in "ALL SHOPS" context
        Shop::setContext(Shop::CONTEXT_ALL);

        foreach ($command->getLanguageIds() as $languageId) {
            $language = $this->getLegacyLanguageObject($languageId);

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

            if (false === $language->delete()) {
                throw new LanguageException(sprintf('Failed to delete language "%s"', $language->iso_code));
            }
        }
        $this->robotsTextFileGenerator->generateFile();
    }
}
