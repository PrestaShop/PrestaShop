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

namespace PrestaShop\PrestaShop\Core\Domain\Meta\Command;

use PrestaShop\PrestaShop\Core\Domain\Meta\Exception\MetaConstraintException;

/**
 * Class SaveMetaCommand is responsible for defining the abstraction for AddMetaCommand and EditMetaCommand.
 */
abstract class SaveMetaCommand
{
    /**
     * Validates page name.
     *
     * @param string $pageName
     *
     * @throws MetaConstraintException
     */
    protected function validatePageName($pageName)
    {
        if (!is_string($pageName) || !$pageName) {
            throw new MetaConstraintException(
                sprintf('Invalid Meta page name %s', var_export($pageName, true)),
                MetaConstraintException::INVALID_PAGE_NAME
            );
        }
    }

    /**
     * Validates rewrite url in case of it's not the index page which can have empty value as rewrite url.
     *
     * @param array $rewriteUrl
     * @param string $pageName
     *
     * @throws MetaConstraintException
     */
    protected function validateRewriteUrl(array $rewriteUrl, $pageName)
    {
        if ('index' !== $pageName && empty(array_filter($rewriteUrl))) {
            throw new MetaConstraintException(
                'Meta rewrite url is required',
                MetaConstraintException::INVALID_URL_REWRITE
            );
        }
    }
}
