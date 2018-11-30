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

namespace PrestaShop\PrestaShop\Adapter\Meta\CommandHandler;

/**
 * todo: inject some kind of validator service which will be used in constraints as well etc...
 * Class AbstractSaveMetaHandler
 */
abstract class AbstractSaveMetaHandler
{
    /**
     * Checks if url rewrite is set. It is not required only for index page so it can be skipped in such case.
     *
     *
     * @param string $pageName
     * @param string[] $rewriteUrl
     *
     * @return bool
     */
    protected function doesUrlRewriteExists($pageName, array $rewriteUrl)
    {
        return 'index' === $pageName ? true : !empty(array_filter($rewriteUrl));
    }
}
