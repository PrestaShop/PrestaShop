<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\PrestaShop\Core\Addon\Theme\Theme;
use Symfony\Component\Translation\MessageCatalogue;

/**
 * Main translation provider for the Front Office
 */
class FrontOfficeProvider extends AbstractProvider implements UseDefaultCatalogueInterface
{
    /**
     * @deprecated Should be removed in 10.0
     */
    public const DEFAULT_THEME_NAME = _PS_DEFAULT_THEME_NAME_;

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return [
            '^Shop*',
            '^Modules(.*)Shop',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            '#^Shop*#',
            '#^Modules(.*)Shop#',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'front';
    }

    /**
     * @param null $themeName
     *
     * @return MessageCatalogue
     */
    public function getDatabaseCatalogue($themeName = null)
    {
        if (null === $themeName) {
            $themeName = Theme::getDefaultTheme();
        }

        return parent::getDatabaseCatalogue($themeName);
    }

    /**{@inheritdoc}
     */
    public function getDefaultResourceDirectory()
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default';
    }
}
