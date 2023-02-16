<?php

namespace PrestaShop\PrestaShop\Adapter\Hook;

use Hook;
use PrestaShop\PrestaShop\Core\Hook\HookExecutorInterface;

class HookAdapter implements HookExecutorInterface
{
    public function exec(
        string $hookName,
        array $hook_args = [],
        ?int $id_module = null,
        bool $array_return = false,
        bool $check_exceptions = true,
        ?int $id_shop = null,
        bool $chain = false
    ) {
        return Hook::exec(
            $hookName,
            $hook_args,
            $id_module,
            $array_return,
            $check_exceptions,
            false,
            $id_shop,
            $chain
        );
    }
}
