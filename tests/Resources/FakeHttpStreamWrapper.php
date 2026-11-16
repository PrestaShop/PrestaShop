<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Resources;

/**
 * Replaces the http stream wrapper so that code reading remote files with fopen() can be tested
 * without any network access: every URL serves the same body, and the opened URLs are recorded.
 *
 * Always call unregister() after use, the default http wrapper is unavailable until then.
 */
final class FakeHttpStreamWrapper
{
    public static string $body = '';

    /**
     * @var string[]
     */
    public static array $openedUrls = [];

    /**
     * Set by PHP when a stream context is given
     *
     * @var resource|null
     */
    public $context;

    private int $position = 0;

    public static function register(string $body): void
    {
        self::$body = $body;
        self::$openedUrls = [];
        stream_wrapper_unregister('http');
        stream_wrapper_register('http', self::class);
    }

    public static function unregister(): void
    {
        stream_wrapper_restore('http');
    }

    public function stream_open(string $path, string $mode, int $options, ?string &$openedPath): bool
    {
        self::$openedUrls[] = $path;
        $this->position = 0;

        return true;
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
