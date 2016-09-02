<?php
/**
 * 2007-2016 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author     PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2016 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShopBundle\Translation\Provider;

class ThemeProvider extends AbstractProvider
{
    private $themeName;

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomains()
    {
        return array('*');
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array('*');
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'theme';
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageCatalogue()
    {
        $xlfCatalogue = $this->getXliffCatalogue();

        $databaseCatalogue = $this->getDatabaseCatalogue();

        // Merge database catalogue to xliff catalogue
        $xlfCatalogue->addCatalogue($databaseCatalogue);

        return $xlfCatalogue;
    }

    /**
     * @return string Path to app/themes/{themeName}/translations/{locale}
     */
    public function getResourceDirectory()
    {
        return $this->resourceDirectory.'/'.$this->themeName.'/translations/'.$this->getLocale();
    }

    /**
     * @param $themeName string The theme name
     *
     * @return self
     */
    public function setThemeName($themeName)
    {
        $this->themeName = $themeName;

        return $this;
    }
}
