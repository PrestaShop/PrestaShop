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

namespace PrestaShop\PrestaShop\Core\Product\Search;

use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * This class is not a factory but a provider of default Sort Orders.
 *
 * @deprecated since 1.7.6 and to be removed in the next major: use SortOrdersCollection:getDefaults instead.
 */
class SortOrderFactory
{
    /**
     * @var TranslatorInterface the translator
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        @trigger_error(
            'This class is deprecated since 1.7.6, use SortOrdersCollection::getDefaults instead.',
            E_USER_DEPRECATED
        );
        $this->translator = $translator;
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getDefaultSortOrders()
    {
        return (new SortOrdersCollection($this->translator))->getDefaults();
    }
}
