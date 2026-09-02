<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Theme\CommandHandler;

use PrestaShop\PrestaShop\Core\CommandBus\Attributes\AsCommandHandler;
use PrestaShop\PrestaShop\Core\Domain\Theme\Command\AdaptThemeToRTLLanguagesCommand;
use PrestaShop\PrestaShop\Core\Domain\Theme\Exception\CannotAdaptThemeToRTLLanguagesException;
use PrestaShop\PrestaShop\Core\Localization\RTL\Exception\GenerationException;
use PrestaShop\PrestaShop\Core\Localization\RTL\StyleSheetProcessorFactoryInterface;

/**
 * Class AdaptThemeToRTLLanguagesHandler
 */
#[AsCommandHandler]
final class AdaptThemeToRTLLanguagesHandler implements AdaptThemeToRTLLanguagesHandlerInterface
{
    /**
     * @var StyleSheetProcessorFactoryInterface
     */
    private $stylesheetProcessorFactory;

    /**
     * @param StyleSheetProcessorFactoryInterface $stylesheetProcessorFactory
     */
    public function __construct(StyleSheetProcessorFactoryInterface $stylesheetProcessorFactory)
    {
        $this->stylesheetProcessorFactory = $stylesheetProcessorFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(AdaptThemeToRTLLanguagesCommand $command)
    {
        $plainThemeName = $command->getThemeName()->getValue();

        try {
            $this->stylesheetProcessorFactory
                ->create()
                ->setProcessFOThemes([$plainThemeName])
                ->process()
            ;
        } catch (GenerationException $e) {
            throw new CannotAdaptThemeToRTLLanguagesException(sprintf('Cannot adapt "%s" theme to RTL languages.', $plainThemeName), 0, $e);
        }
    }
}
