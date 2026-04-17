<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Provider;

/**
 * Translation provider for native modules (maintained by the core team)
 * Translations are provided by Crowdin.
 */
class ModulesProvider extends AbstractProvider implements UseDefaultCatalogueInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return ['^Modules[A-Z]'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return ['#^Modules[A-Z]#'];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'modules';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResourceDirectory()
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default';
    }
}
