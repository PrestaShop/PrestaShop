<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Form\ChoiceProvider;

use Employee;
use PrestaShop\PrestaShop\Core\Form\FormChoiceProviderInterface;

/**
 * @internal
 */
final class EmployeeNameByIdChoiceProvider implements FormChoiceProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function getChoices()
    {
        $employees = Employee::getEmployees();

        $choices = [];

        foreach ($employees as $employee) {
            $name = sprintf('%s. %s', substr($employee['firstname'], 0, 1), $employee['lastname']);

            $choices[$name] = (int) $employee['id_employee'];
        }

        return $choices;
    }
}
