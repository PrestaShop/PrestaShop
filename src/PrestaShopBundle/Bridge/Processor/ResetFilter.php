<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShopBundle\Bridge\Processor;

use Context;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShopBundle\Bridge\Helper\HelperListConfiguration;
use PrestaShopBundle\Bridge\Utils\CookieFilterUtils;
use Symfony\Component\HttpFoundation\Request;
use Tools;

/**
 * Process action for reset filters
 */
class ResetFilter
{
    /**
     * @var Context
     */
    private $context;

    public function __construct(LegacyContext $legacyContext)
    {
        $this->context = $legacyContext->getContext();
    }

    public function resetFilters(HelperListConfiguration $helperListConfiguration, Request $request = null): void
    {
        $prefix = CookieFilterUtils::getCookieByPrefix($helperListConfiguration->controllerNameLegacy);
        $filters = $this->context->cookie->getFamily($prefix . $helperListConfiguration->listId . 'Filter_');
        foreach ($filters as $cookie_key => $filter) {
            if (strncmp($cookie_key, $prefix . $helperListConfiguration->listId . 'Filter_', 7 + Tools::strlen($prefix . $helperListConfiguration->listId)) == 0) {
                $key = substr($cookie_key, 7 + Tools::strlen($prefix . $helperListConfiguration->listId));
                if (is_array($helperListConfiguration->fieldsList) && array_key_exists($key, $helperListConfiguration->fieldsList)) {
                    $this->context->cookie->$cookie_key = null;
                }
                $request->request->remove(str_replace($prefix, '', $cookie_key));
                unset($this->context->cookie->$cookie_key);
            }
        }

        if (isset($this->context->cookie->{'submitFilter' . $helperListConfiguration->listId})) {
            unset($this->context->cookie->{'submitFilter' . $helperListConfiguration->listId});
        }
        if (isset($this->context->cookie->{$prefix . $helperListConfiguration->listId . 'Orderby'})) {
            unset($this->context->cookie->{$prefix . $helperListConfiguration->listId . 'Orderby'});
        }
        if (isset($this->context->cookie->{$prefix . $helperListConfiguration->listId . 'Orderway'})) {
            unset($this->context->cookie->{$prefix . $helperListConfiguration->listId . 'Orderway'});
        }

        //$request->request->

        unset(
            $helperListConfiguration->filterHaving,
            $helperListConfiguration->having
        );
    }
}
