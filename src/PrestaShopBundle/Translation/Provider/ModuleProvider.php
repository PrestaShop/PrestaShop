<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Translation\Provider;

use PrestaShop\TranslationToolsBundle\Translation\Helper\DomainHelper;

/**
 * Translation provider for a specific native module (maintained by the core team)
 * Used mainly for the display in the Translations Manager of the Back Office.
 */
class ModuleProvider extends AbstractProvider implements SearchProviderInterface, UseModuleInterface
{
    /**
     * @var string the module name
     */
    private $moduleName;

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return ['^' . preg_quote(DomainHelper::buildModuleBaseDomain($this->moduleName)) . '([A-Z]|$)'];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return ['#^' . preg_quote(DomainHelper::buildModuleBaseDomain($this->moduleName)) . '([A-Z]|\.|$)#'];
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'module';
    }

    /**
     * {@inheritdoc}
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultResourceDirectory()
    {
        return $this->resourceDirectory . DIRECTORY_SEPARATOR . 'default';
    }
}
