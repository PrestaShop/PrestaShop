<?php

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
