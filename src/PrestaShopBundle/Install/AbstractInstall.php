<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Install;

use NullLogger;
use PrestaShopLoggerInterface;

abstract class AbstractInstall
{
    /**
     * @var LanguageList
     */
    public $language;

    /**
     * @var \PrestaShopBundle\Translation\Translator
     */
    public $translator;

    /**
     * @var array List of errors
     */
    protected $errors = [];
    /**
     * @var array List of warnings
     */
    protected $warnings = [];

    /**
     * @var PrestaShopLoggerInterface|null
     */
    protected $logger;

    public function __construct()
    {
        $this->language = LanguageList::getInstance();
    }

    public function setError($errors)
    {
        if (!is_array($errors)) {
            $errors = [$errors];
        }

        $this->errors = array_merge($this->errors, $errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function resetErrors(): void
    {
        $this->errors = [];
    }

    public function setWarning($warnings): void
    {
        if (!is_array($warnings)) {
            $warnings = [$warnings];
        }

        $this->warnings = array_merge($this->warnings, $warnings);
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }

    public function resetWarnings(): void
    {
        $this->warnings = [];
    }

    public function setTranslator($translator)
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * @return PrestaShopLoggerInterface;
     */
    public function getLogger(): PrestaShopLoggerInterface
    {
        if (null === $this->logger) {
            $this->logger = new NullLogger();
        }

        return $this->logger;
    }

    /**
     * @param PrestaShopLoggerInterface $logger
     */
    public function setLogger(PrestaShopLoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
