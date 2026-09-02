<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\ChoiceProvider;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ThemeZipChoiceProvider provides choices for uploaded Front Office theme zips.
 */
final class ThemeZipChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $themeZipsFinder = (new Finder())
            ->in($this->configuration->get('_PS_ALL_THEMES_DIR_'))
            ->files()
            ->name('*.zip')
        ;

        $themeZips = [];

        /** @var SplFileInfo $themeZip */
        foreach ($themeZipsFinder as $themeZip) {
            $themeZips[$themeZip->getFilename()] = $themeZip->getFilename();
        }

        return $themeZips;
    }
}
