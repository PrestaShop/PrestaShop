<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Form\IdentifiableObject\Handler;

/**
 * Stores results for handling forms.
 */
class FormHandlerResult implements FormHandlerResultInterface
{
    /**
     * @var bool
     */
    private $isValid;

    /**
     * @var bool
     */
    private $isSubmitted;

    /**
     * @var int|null
     */
    private $identifiableObjectId;

    /**
     * @param int|null $identifiableObjectId ID of identifiable object or null if it does not exist
     * @param bool $isSubmitted
     * @param bool $isValid
     */
    private function __construct($identifiableObjectId, $isSubmitted, $isValid)
    {
        $this->identifiableObjectId = $identifiableObjectId;
        $this->isSubmitted = $isSubmitted;
        $this->isValid = $isValid;
    }

    /**
     * Creates successful form handler result with identifiable object id.
     *
     * @param int $identifiableObjectId
     *
     * @return FormHandlerResult
     */
    public static function createWithId($identifiableObjectId)
    {
        return new self(
            $identifiableObjectId,
            true,
            true
        );
    }

    /**
     * Creates form handler result when form which was provided form handling was not submitted
     *
     * @return FormHandlerResult
     */
    public static function createNotSubmitted()
    {
        return new self(
            null,
            false,
            false
        );
    }

    /**
     * Creates result for submitted but not valid form
     *
     * @return FormHandlerResult
     */
    public static function createSubmittedButNotValid()
    {
        return new self(
            null,
            true,
            false
        );
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * @return bool
     */
    public function isSubmitted()
    {
        return $this->isSubmitted;
    }

    /**
     * @return int|mixed|null
     */
    public function getIdentifiableObjectId(): mixed
    {
        return $this->identifiableObjectId;
    }
}
