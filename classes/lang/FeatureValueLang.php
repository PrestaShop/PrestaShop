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

class FeatureValueLangCore extends DataLangCore
{
    // Don't replace domain in init() with $this->domain for translation parsing
    protected $domain = 'Shop.Demo.Catalog';

    protected $keys = array('id_feature_value');

    protected $fieldsToUpdate = array('value');

    protected function init()
    {
        $this->fieldNames = array(
            'value' => array(
                md5('Polyester')
                    => $this->translator->trans('Polyester', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Wool')
                    => $this->translator->trans('Wool', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Viscose')
                    => $this->translator->trans('Viscose', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Elastane')
                    => $this->translator->trans('Elastane', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Cotton')
                    => $this->translator->trans('Cotton', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Silk')
                    => $this->translator->trans('Silk', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Suede')
                    => $this->translator->trans('Suede', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Straw')
                    => $this->translator->trans('Straw', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Leather')
                    => $this->translator->trans('Leather', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Classic')
                    => $this->translator->trans('Classic', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Casual')
                    => $this->translator->trans('Casual', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Military')
                    => $this->translator->trans('Military', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Girly')
                    => $this->translator->trans('Girly', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Rock')
                    => $this->translator->trans('Rock', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Basic')
                    => $this->translator->trans('Basic', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Dressy')
                    => $this->translator->trans('Dressy', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Short Sleeve')
                    => $this->translator->trans('Short Sleeve', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Colorful Dress')
                    => $this->translator->trans('Colorful Dress', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Short Dress')
                    => $this->translator->trans('Short Dress', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Midi Dress')
                    => $this->translator->trans('Midi Dress', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('Maxi Dress')
                    => $this->translator->trans('Maxi Dress', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('2.75 in')
                    => $this->translator->trans('2.75 in', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('2.06 in')
                    => $this->translator->trans('2.06 in', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('49.2 g')
                    => $this->translator->trans('49.2 g', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('0.26 in')
                    => $this->translator->trans('0.26 in', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('1.07 in')
                    => $this->translator->trans('1.07 in', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('1.62 in')
                    => $this->translator->trans('1.62 in', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('15.5 g')
                    => $this->translator->trans('15.5 g', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('0.41 in (clip included)')
                    => $this->translator->trans('0.41 in (clip included)', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('4.33 in')
                    => $this->translator->trans('4.33 in', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('2.76 in')
                    => $this->translator->trans('2.76 in', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('120g')
                    => $this->translator->trans('120g', array(), 'Shop.Demo.Catalog', $this->locale),

                md5('0.31 in')
                    => $this->translator->trans('0.31 in', array(), 'Shop.Demo.Catalog', $this->locale),
            ),
        );
    }
}
