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

namespace PrestaShop\PrestaShop\Core\ConstraintValidator;

use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\State\Query\IsUniqueStateIsoCode;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Unique state iso code validator
 */
final class UniqueStateIsoCodeValidator extends ConstraintValidator
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var CommandBusInterface
     */
    private $queryBus;

    /**
     * @param TranslatorInterface $translator
     * @param CommandBusInterface $queryBus
     */
    public function __construct(TranslatorInterface $translator, CommandBusInterface $queryBus)
    {
        $this->translator = $translator;
        $this->queryBus = $queryBus;
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $isUniqueStateIsoCodeQuery = new IsUniqueStateIsoCode($value['iso_code']);

        if (isset($value['id_state']) && $value['id_state'] instanceof StateId) {
            /** @var StateId $stateId */
            $stateId = $value['id_state'];

            $isUniqueStateIsoCodeQuery->setExcludeStateId($stateId->getValue());
        }

        /** @var bool $isFound */
        $isFound = $this->queryBus->handle($isUniqueStateIsoCodeQuery);

        if ($isFound) {
            $this->context
                ->buildViolation(
                    $this->translator->trans(
                        'This ISO code already exists. You cannot create two states with the same ISO code.',
                        [],
                        'Admin.International.Notification'
                    )
                )
                ->atPath('[iso_code]')
                ->addViolation();
        }
    }
}
