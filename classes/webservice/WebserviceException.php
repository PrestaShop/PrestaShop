<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
class WebserviceExceptionCore extends Exception
{
    protected $status;
    /**
     * @var string
     */
    protected $wrong_value;
    /**
     * @var array
     */
    protected $available_values;
    protected $type;

    public const SIMPLE = 0;
    public const DID_YOU_MEAN = 1;

    public function __construct($message, $code)
    {
        $exception_code = $code;
        if (is_array($code)) {
            $exception_code = $code[0];
            $this->setStatus($code[1]);
        }
        parent::__construct($message, $exception_code);
        $this->type = self::SIMPLE;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    public function setStatus($status)
    {
        if (Validate::isInt($status)) {
            $this->status = $status;
        }

        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getWrongValue()
    {
        return $this->wrong_value;
    }

    /**
     * @param string $wrong_value
     * @param array $available_values
     *
     * @return self
     */
    public function setDidYouMean($wrong_value, $available_values)
    {
        $this->type = self::DID_YOU_MEAN;
        $this->wrong_value = $wrong_value;
        $this->available_values = $available_values;

        return $this;
    }

    public function getAvailableValues()
    {
        return $this->available_values;
    }
}
