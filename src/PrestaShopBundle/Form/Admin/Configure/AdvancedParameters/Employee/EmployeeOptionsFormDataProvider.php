<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Employee;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Class EmployeeOptionsFormDataProvider manages data for employee options form.
 */
final class EmployeeOptionsFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $employeeOptionsConfiguration;

    /**
     * @param DataConfigurationInterface $employeeOptionsConfiguration
     */
    public function __construct(DataConfigurationInterface $employeeOptionsConfiguration)
    {
        $this->employeeOptionsConfiguration = $employeeOptionsConfiguration;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        return $this->employeeOptionsConfiguration->getConfiguration();
    }

    /**
     * {@inheritdoc}
     */
    public function setData(array $data)
    {
        return $this->employeeOptionsConfiguration->updateConfiguration($data);
    }
}
