<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Provider;

interface SearchProviderInterface extends ProviderInterface, UseDefaultCatalogueInterface, XliffCatalogueInterface, DatabaseCatalogueInterface
{
    /**
     * @param string $domain
     *
     * @return self
     */
    public function setDomain($domain);

    /**
     * @param string $locale
     *
     * @return self
     */
    public function setLocale($locale);
}
