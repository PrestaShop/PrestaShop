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

namespace PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints;

use PrestaShop\PrestaShop\Core\ConstraintValidator\PasswordValidator;
use Symfony\Component\Validator\Constraint;

/**
 * Class Password is responsible of validating customer name according to several patterns.
 */
final class Password extends Constraint
{
  public $invalidLengthMessage = 'Password length must be between %min% and %max% characters.';

  public $tooWeakMessage = 'The password doesn\'t meet required strength requirement.';

  public $emptyMessage = 'This field cannot be empty.';

  public $minScore;

  public $minLength;

  public $maxLength;

  public $passwordRequired = true;

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return PasswordValidator::class;
    }

    public function getRequiredOptions()
    {
        return ['minScore', 'minLength', 'maxLength'];
    }
}
