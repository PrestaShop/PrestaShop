<?php

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component;

use Link;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/quick_access.html.twig')]
class QuickAccess
{
    public array $quickAccess;
    public Link $link;
    public string $quickAccessCurrentLinkIcon;
    public string $quickAccessCurrentLinkName;
}
