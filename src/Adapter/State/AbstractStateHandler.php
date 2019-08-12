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

namespace PrestaShop\PrestaShop\Adapter\State;

use Country;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CleanHtml;
use PrestaShop\PrestaShop\Core\Domain\Country\Exception\CountryNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateConstraintException;
use PrestaShop\PrestaShop\Core\Domain\State\Exception\StateNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\State\ValueObject\StateId;
use PrestaShop\PrestaShop\Core\Domain\Zone\Exception\ZoneNotFoundException;
use PrestaShopException;
use State;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Zone;

/**
 * Abstract state handler
 */
abstract class AbstractStateHandler
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param StateId $stateId
     *
     * @return State
     *
     * @throws StateNotFoundException
     */
    protected function getState(StateId $stateId)
    {
        $stateIdValue = $stateId->getValue();

        try {
            $state = new State($stateIdValue);
        } catch (PrestaShopException $e) {
            throw new StateNotFoundException(
                sprintf('State with id "%s" was not found.', $stateId->getValue())
            );
        }

        if ($state->id !== $stateId->getValue()) {
            throw new StateNotFoundException(
                sprintf('State with id "%s" was not found.', $stateId->getValue())
            );
        }

        return $state;
    }

    /**
     * @param int $id
     *
     * @throws CountryNotFoundException
     */
    protected function assertCountryWithIdExists(int $id)
    {
        try {
            $country = new Country($id);
        } catch (PrestaShopException $e) {
            throw new CountryNotFoundException(
                sprintf('Country with id "%s" was not found.', $id)
            );
        }

        if ($country->id !== $id) {
            throw new CountryNotFoundException(
                sprintf('Country with id "%s" was not found.', $id)
            );
        }
    }

    /**
     * @param int $id
     *
     * @throws ZoneNotFoundException
     */
    protected function assertZoneWithIdExists(int $id)
    {
        try {
            $zone = new Zone($id);
        } catch (PrestaShopException $e) {
            throw new ZoneNotFoundException(
                sprintf('Zone with id "%s" was not found.', $id)
            );
        }

        if ($zone->id !== $id) {
            throw new ZoneNotFoundException(
                sprintf('Zone with id "%s" was not found.', $id)
            );
        }
    }

    /**
     * @param string $field
     * @param int $code
     *
     * @throws StateConstraintException
     */
    protected function assertFieldContainsCleanHtml(string $field, int $code)
    {
        $errors = $this->validator->validate($field, new CleanHtml());

        if (0 !== count($errors)) {
            throw new StateConstraintException(sprintf(
                'String "%s" contains javascript events or script tags',
                $field
            ),
                $code
            );
        }
    }

    /**
     * @param bool $active
     *
     * @throws StateConstraintException
     */
    protected function assertIsBool($active)
    {
        if (!is_bool($active)) {
            throw new StateConstraintException(
                sprintf(
                    'Unexpected type of active. Expected bool, got "%s"',
                    var_export($active, true)
                ),
                StateConstraintException::INVALID_STATE
            );
        }
    }
}
