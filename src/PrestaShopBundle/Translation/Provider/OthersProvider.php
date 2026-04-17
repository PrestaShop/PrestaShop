<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Provider;

/**
 * Translations provider for keys not yet put in the right domain.
 * Equivalent to so-called main "messages" domain in the Symfony ecosystem.
 */
class OthersProvider extends AbstractProvider implements UseDefaultCatalogueInterface
{
    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return ['^messages*'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return ['#^messages*#'];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'others';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResourceDirectory()
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default';
    }
}
