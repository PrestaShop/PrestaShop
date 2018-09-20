<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\HelperDoc;

/**
 * Class MetaPageHelperDocLinkProvider is responsible for providing "Learn more" link in different languages for helper
 * block located in Shop parameters -> Traffic & Seo -> Seo & Urls page.
 */
class MetaPageHelperDocLinkProvider implements HelperDocLinkProviderInterface
{
    /**
     * @var string
     */
    private $contextIsoCode;

    /**
     * MetaPageHelperDocLinkProvider constructor.
     *
     * @param string $contextIsoCode
     */
    public function __construct($contextIsoCode)
    {
        $this->contextIsoCode = $contextIsoCode;
    }

    /**
     * {@inheritdoc}
     */
    public function getLink()
    {
        $link = 'http://doc.prestashop.com/display/PS17/SEO+and+URLs';

        switch ($this->contextIsoCode) {
            case 'fr':
                $link = 'http://doc.prestashop.com/display/PS17/SEO+et+URL';
                break;
            case 'es':
                $link = 'http://doc.prestashop.com/display/PS17/SEO+y+URLs';
                break;
            case 'it':
                $link = 'http://doc.prestashop.com/display/PS17/SEO+e+URL';
        }

        return $link;
    }
}
