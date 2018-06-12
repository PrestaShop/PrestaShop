<?php
/**
 * 2007-2018 PrestaShop
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

namespace PrestaShopBundle\Translation\KeysReference\Xlang;

use PrestaShopBundle\Translation\TranslatorComponent as Translator;

class ThemeLang
{
    /** @var Translator  */
    protected $translator;

    /** @var string */
    protected $locale;

    protected function init()
    {
        $this->translator->trans('Full width', array(), 'Admin.Design.Feature', $this->locale);
        $this->translator->trans('Three columns', array(), 'Admin.Design.Feature', $this->locale);
        $this->translator->trans('Two columns, small left column', array(), 'Admin.Design.Feature', $this->locale);
        $this->translator->trans('Two columns, small right column', array(), 'Admin.Design.Feature', $this->locale);

        $this->translator->trans('No side columns, ideal for distraction-free pages such as product pages.', array(), 'Admin.Design.Feature', $this->locale);

        $this->translator->trans('One large central column and 2 side columns.', array(), 'Admin.Design.Feature', $this->locale);

        $this->translator->trans('Two columns with a small left column.', array(), 'Admin.Design.Feature', $this->locale);

        $this->translator->trans('Two columns with a small right column.', array(), 'Admin.Design.Feature', $this->locale);
    }
}
