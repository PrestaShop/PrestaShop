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
trans('Dear Customer,

Regards,
Customer service',
    'Admin.Shopparameters.Feature'
);

trans('We are currently updating our shop and will be back really soon.
Thanks for your patience.',
    'Admin.Shopparameters.Feature'
);

// PS_INVOICE_PREFIX on configuration_lang table
trans('#IN', 'Admin.Shopparameters.Feature');

// PS_DELIVERY_PREFIX on configuration_lang table
trans('#DE', 'Admin.Shopparameters.Feature');

// PS_RETURN_PREFIX on configuration_lang table
trans('#RE', 'Admin.Shopparameters.Feature');

// PS_RETURN_PREFIX on configuration_lang table - No translate word per word but adapting for your language
trans('a|about|above|after|again|against|all|am|an|and|any|are|aren|as|at|be|because|been|before|being|below|between|both|but|by|can|cannot|could|couldn|did|didn|do|does|doesn|doing|don|down|during|each|few|for|from|further|had|hadn|has|hasn|have|haven|having|he|ll|her|here|hers|herself|him|himself|his|how|ve|if|in|into|is|isn|it|its|itself|let|me|more|most|mustn|my|myself|no|nor|not|of|off|on|once|only|or|other|ought|our|ours|ourselves|out|over|own|same|shan|she|should|shouldn|so|some|such|than|that|the|their|theirs|them|themselves|then|there|these|they|re|this|those|through|to|too|under|until|up|very|was|wasn|we|were|weren|what|when|where|which|while|who|whom|why|with|won|would|wouldn|you|your|yours|yourself|yourselves',
    'Admin.Shopparameters.Feature'
);

// NW_CONDITIONS on configuration_lang table - From ps_emailsubscription module
trans('You may unsubscribe at any moment. For that purpose, please find our contact info in the legal notice.',
    'Admin.Shopparameters.Feature'
);

// PS_LABEL_IN_STOCK_PRODUCTS on configuration_lang table
trans('In Stock', 'Admin.Shopparameters.Feature');

// PS_LABEL_OOS_PRODUCTS_BOA on configuration_lang table
trans('Product available for orders', 'Admin.Shopparameters.Feature');

// PS_LABEL_OOS_PRODUCTS_BOD on configuration_lang table
trans('Out-of-Stock', 'Admin.Shopparameters.Feature');
