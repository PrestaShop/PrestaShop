<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Provider;

/**
 * Main translation provider of the Back Office
 */
class BackOfficeProvider extends AbstractProvider implements UseDefaultCatalogueInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return [
            '^Admin[A-Z]',
            '^Modules[A-Z](.*)Admin',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            '#^Admin[A-Z]#',
            '#^Modules[A-Z](.*)Admin#',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'back';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResourceDirectory()
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default';
    }
}
