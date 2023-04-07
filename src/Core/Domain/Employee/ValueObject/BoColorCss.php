namespace PrestaShop\PrestaShop\Core\Domain\Employee\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Employee\Exception\EmployeeConstraintException;

/**
 * Carries employee's bo color css
 */
class BoColorCss
{
    /**
     * @var int Maximum allowed length for bo color css
     */
    public const MAX_LENGTH = 64;

    /**
     * @var string
     */
    private $boColorCss;

    /**
     * @param string $boColorCss
     */
    public function __construct($boColorCss)
    {
        $this->assertBoColorCssDoesNotExceedAllowedLength($boColorCss);
        $this->assertBoColorCssIsValid($boColorCss);

        $this->boColorCss = $boColorCss;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->boColorCss;
    }

    /**
     * @param string $boColorCss
     *
     * @throws EmployeeConstraintException
     */
    private function assertBoColorCssIsValid($boColorCss)
    {
        $matchesBoColorCssPattern = preg_match('/^[a-zA-Z0-9_\-\.]{1,64}\.css$/u', stripslashes($boColorCss));

        if (!$matchesBoColorCssPattern) {
            throw new EmployeeConstraintException(sprintf('Employee bo color css %s is invalid', var_export($boColorCss, true)), EmployeeConstraintException::INVALID_BO_COLOR_CSS);
        }
    }

    /**
     * @param string $boColorCss
     *
     * @throws EmployeeConstraintException
     */
    private function assertBoColorCssDoesNotExceedAllowedLength($boColorCss)
    {
        if (self::MAX_LENGTH < strlen($boColorCss)) {
            throw new EmployeeConstraintException(sprintf('Employee bo color css is too long. Max allowed length is %s', self::MAX_LENGTH), EmployeeConstraintException::INVALID_BO_COLOR_CSS);
        }
    }
}
