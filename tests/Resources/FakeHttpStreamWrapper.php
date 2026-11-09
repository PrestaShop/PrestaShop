<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace Tests\Resources;

/**
 * Replaces the http stream wrapper so that code reading remote files with fopen() can be tested
 * without any network access: every URL serves the same body, or cannot be opened when the wrapper
 * is registered as unreachable, and the opened URLs are recorded.
 *
 * Always call unregister() after use, the default http wrapper is unavailable until then.
 * It can be called even when the wrapper was not registered.
 */
final class FakeHttpStreamWrapper
{
    /**
     * @var string
     */
    public static $body = '';

    /**
     * @var string[]
     */
    public static $openedUrls = [];

    /**
     * @var bool
     */
    public static $reachable = true;

    /**
     * @var bool
     */
    private static $registered = false;

    /**
     * Set by PHP when a stream context is given
     *
     * @var resource|null
     */
    public $context;

    /**
     * @var int
     */
    private $position = 0;

    public static function register(string $body, bool $reachable = true): void
    {
        self::$body = $body;
        self::$openedUrls = [];
        self::$reachable = $reachable;
        stream_wrapper_unregister('http');
        stream_wrapper_register('http', self::class);
        self::$registered = true;
    }

    public static function unregister(): void
    {
        if (self::$registered) {
            stream_wrapper_restore('http');
            self::$registered = false;
        }
    }

    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool
    {
        self::$openedUrls[] = $path;
        $this->position = 0;

        return self::$reachable;
    }

    public function stream_read(int $count): string
    {
        $chunk = substr(self::$body, $this->position, $count);
        $this->position += strlen($chunk);

        return $chunk;
    }

    public function stream_eof(): bool
    {
        return $this->position >= strlen(self::$body);
    }

    public function stream_close(): void
    {
    }

    public function stream_stat(): array
    {
        return [];
    }
}
