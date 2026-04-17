<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\DependencyInjection;

use Closure;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;
use Symfony\Component\DependencyInjection\Exception\EnvNotFoundException;

final class RuntimeConstEnvVarProcessor implements EnvVarProcessorInterface
{
    public function getEnv($prefix, $name, Closure $getEnv): mixed
    {
        $exploded = explode(':', $name);
        if (count($exploded) !== 2 || $exploded[0] !== 'runtime' || !defined($exploded[1])) {
            throw new EnvNotFoundException($name);
        }

        return constant($exploded[1]);
    }

    public static function getProvidedTypes(): array
    {
        return [
            'const' => 'bool|int|float|string|array',
        ];
    }
}
