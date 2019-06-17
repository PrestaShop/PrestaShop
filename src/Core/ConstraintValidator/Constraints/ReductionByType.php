<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

use PrestaShop\PrestaShop\Core\ConstraintValidator\ReductionByTypeValidator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;

/**
 * Provides reduction validation by reduction type
 */
final class ReductionByType extends Constraint
{
    public $message = '%s is invalid.';

    /**
     * @var string
     */
    public $reductionTypePath;

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return ReductionByTypeValidator::class;
    }

    public function __construct($options = null)
    {
        if (is_array($options)) {
            if (!isset($options['reductionTypePath'])) {
                throw new ConstraintDefinitionException(sprintf('The "%s" constraint requires "reductionTypePath" option to be set.', \get_class($this)));
            }
        }
        parent::__construct($options);
    }
}
