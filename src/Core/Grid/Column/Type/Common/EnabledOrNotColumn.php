<?php

namespace PrestaShop\PrestaShop\Core\Grid\Column\Type\Common;

use PrestaShop\PrestaShop\Core\Grid\Column\AbstractColumn;

/**
 * Column used to display booleans as icons for "enabled" or "active" properties
 */
final class EnabledOrNotColumn extends AbstractColumn
{
    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'enabled_or_not';
    }
}
