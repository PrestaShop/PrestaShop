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

namespace Tests\Integration\Behaviour\Features\Context\Util;

/**
 * Allows to mock the php://input stream
 * Without this stream, we can't write on the php://input stream because it's readyonly
 *
 * @example
 *   stream_wrapper_unregister('php');
 *   stream_register_wrapper('php', StreamWrapperPHP::class);
 *   file_put_contents('php://input', 'xml=' . $xmlVariable);
 */
class StreamWrapperPHP
{
    /**
     * @var int
     */
    protected $index = 0;
    protected $length = null;
    protected $data = '';

    public $context;

    /**
     * Register this wrapper as a replacement for php protocol
     *
     * VERY IMPORTANT do not forget to unregister after use or any use to php protocol might fail this includes
     * php://memory php://temp php://std* and many others which are heavily used by third party libraries like PHPUnit
     */
    public static function register(): void
    {
        // Unregister default stream wrapper for protocol php
        stream_wrapper_unregister('php');

        /*
         * Register itself as the new stream wrapper for php protocol, from now any write/read operations on the php
         * protocol will be intercepted by this class it means that
         *
         *     file_put_contents('php://input', 'xml=' . $postFields);
         *
         * will actually write in the file handled internally by this class instead of the usual input, this allows
         * mocking the input I/O stream thus mocking an input request header
         */
        stream_wrapper_register('php', static::class);
    }

    /**
     * Unregister this wrapper and restores the default one to roll back to usual behaviour
     */
    public static function unregister(): void
    {
        stream_wrapper_unregister('php');
        stream_wrapper_restore('php');
    }

    public function __construct()
    {
        if (file_exists($this->buffer_filename())) {
            $this->data = file_get_contents($this->buffer_filename());
        }
        $this->index = 0;
        $this->length = strlen($this->data);
    }

    protected function buffer_filename(): string
    {
        return sys_get_temp_dir() . '/php_input.txt';
    }

    public function stream_open($path, $mode, $options, &$opened_path): bool
    {
        return true;
    }

    public function stream_close()
    {
    }

    public function stream_stat(): array
    {
        return [];
    }

    public function stream_flush(): bool
    {
        return true;
    }

    public function stream_read(int $count): string
    {
        if (is_null($this->length) === true) {
            $this->length = strlen($this->data);
        }
        $length = min($count, $this->length - $this->index);
        $data = substr($this->data, $this->index);
        $this->index = $this->index + $length;

        return $data;
    }

    public function stream_eof()
    {
        return $this->index >= $this->length;
    }

    public function stream_write($data)
    {
        return file_put_contents($this->buffer_filename(), $data);
    }

    public function unlink()
    {
        if (file_exists($this->buffer_filename())) {
            unlink($this->buffer_filename());
        }
        $this->data = '';
        $this->index = 0;
        $this->length = 0;
    }
}
