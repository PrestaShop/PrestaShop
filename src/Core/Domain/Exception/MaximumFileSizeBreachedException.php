<?php

namespace PrestaShop\PrestaShop\Core\Domain\Exception;

class MaximumFileSizeBreachedException extends DomainException
{
    /**
     * @var int
     */
    private $actualSizeInBytes;

    /**
     * @var int
     */
    private $expectedSizeInBytes;

    /**
     * @param int $actualSizeInBytes
     * @param int $expectedSizeInBytes
     * @param string $message
     * @param int $code
     * @param null $previous
     */
    public function __construct(
        $actualSizeInBytes,
        $expectedSizeInBytes,
        $message = "",
        $code = 0,
        $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->actualSizeInBytes = $actualSizeInBytes;
        $this->expectedSizeInBytes = $expectedSizeInBytes;
    }

    /**
     * @return int
     */
    public function getActualSizeInBytes()
    {
        return $this->actualSizeInBytes;
    }

    /**
     * @return int
     */
    public function getExpectedSizeInBytes()
    {
        return $this->expectedSizeInBytes;
    }

}
