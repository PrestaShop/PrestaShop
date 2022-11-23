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

declare(strict_types=1);

namespace PrestaShopBundle\Form\Exception;

use PrestaShop\PrestaShop\Core\Data\AbstractTypedCollection;
use PrestaShop\PrestaShop\Core\Form\ErrorMessage\ConfigurationErrorCollection;

/** @deprecated and will be removed in 9.0 */
class InvalidConfigurationDataErrorCollection extends AbstractTypedCollection
{
    public function __construct()
    {
        parent::__construct();
        @trigger_error(
            sprintf(
                'The %s class is deprecated since version 8.1 and will be removed in 9. Use the %s class instead.',
                __CLASS__,
                ConfigurationErrorCollection::class
            ),
            E_USER_DEPRECATED
        );
    }

    protected function getType(): string
    {
        return InvalidConfigurationDataError::class;
    }
}
