<?php

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Employee;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;

final class EmployeeNameWithAvatarColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'employee_name_with_avatar';
    }
}
