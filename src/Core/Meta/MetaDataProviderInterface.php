<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Meta;

/**
 * Interface MetaDataProviderInterface defines contract fot MetaDataProvider.
 */
interface MetaDataProviderInterface
{
    /**
     * Gets id by page.
     *
     * @param string $pageName
     *
     * @return int
     */
    public function getIdByPage($pageName);

    /**
     * Gets default page by  meta id.
     *
     * @param int $metaId
     *
     * @return string|null
     */
    public function getDefaultMetaPageNameById($metaId);

    /**
     * Gets module page by meta id.
     *
     * @param int $metaId
     *
     * @return string|null
     */
    public function getModuleMetaPageNameById($metaId);

    /**
     * Gets default pages which are not configured in Seo & urls page.
     *
     * @return array
     */
    public function getDefaultMetaPageNamesExcludingFilled();

    /**
     * Gets module pages which are not configured in Seo & urls page.
     *
     * @return array
     */
    public function getNotConfiguredModuleMetaPageNames();
}
