<?php

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AddSlashesExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('addslashes', 'addslashes'),
        ];
    }
}
