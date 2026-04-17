<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Core\Domain\Customer\QueryResult;

/**
 * Class SentEmailInformation holds information about email sent to customer.
 */
class SentEmailInformation
{
    /**
     * @var string
     */
    private $date;

    /**
     * @var string
     */
    private $language;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $template;

    /**
     * @param string $date
     * @param string $language
     * @param string $subject
     * @param string $template
     */
    public function __construct($date, $language, $subject, $template)
    {
        $this->date = $date;
        $this->language = $language;
        $this->subject = $subject;
        $this->template = $template;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
