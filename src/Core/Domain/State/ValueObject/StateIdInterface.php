<?php
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\State\ValueObject;

interface StateIdInterface
{
    public function getValue(): int;
}
