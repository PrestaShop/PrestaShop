<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use PrestaShop\PrestaShop\Core\ConstraintValidator\AddressStateRequiredValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Validation constraint for address state choice by selected country value
 */
class AddressStateRequired extends Constraint
{
    public $message = 'An address located in a country containing states must have a state selected.';

    public $id_country;

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return AddressStateRequiredValidator::class;
    }

    public function getRequiredOptions()
    {
        return ['id_country'];
    }
}
