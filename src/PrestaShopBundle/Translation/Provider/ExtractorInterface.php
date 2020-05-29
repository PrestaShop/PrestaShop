<?php

namespace PrestaShopBundle\Translation\Provider;

use Symfony\Component\Translation\MessageCatalogueInterface;

interface ExtractorInterface
{
    public function extract(): MessageCatalogueInterface;
}
