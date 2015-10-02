<?php

namespace PrestaShop\PrestaShop\Core\Business\Checkout;

class TermsAndConditions
{
    private $identifier;
    private $links;
    private $rawText;

    public function setText($rawText)
    {
        $links = func_get_args();
        array_shift($links);

        $this->links = $links;
        $this->rawText = $rawText;
        return $this;
    }

    /**
     * Inserts links into the text, replacing all [something] with links to "something", taking
     * URLs from $this->links
     * @return an string of HTML
     */
    public function format()
    {
        $index = 0;
        return preg_replace_callback('/\[(.*?)\]/', function (array $match) use (&$index) {
            if (!isset($this->links[$index])) {
                return $match[1];
            }

            $replacement = '<a href="' . $this->links[$index] . '">' . $match[1] . '</a>';
            ++$index;
            return $replacement;
        }, $this->rawText);
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        return $this;
    }

    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Takes a list of terms and conditions and return them in
     * the format templates expect.
     * @param  array  $conditions An array of TermsAndConditions
     * @return An associative array with condition identifiers as keys and HTML (text + links) as values
     */
    public static function formatForTemplate(array $conditions)
    {
        $formatted = [];

        foreach ($conditions as $arrayOfConditions) {
            if (!is_array($arrayOfConditions)) {
                $arrayOfConditions = [$arrayOfConditions];
            }
            foreach ($arrayOfConditions as $condition) {
                $formatted[$condition->getIdentifier()] = $condition->format();
            }
        }

        return $formatted;
    }
}
