<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
interface WebserviceOutputInterface
{
    public function __construct($languages = []);

    public function setWsUrl($url);

    public function getWsUrl();

    public function getContentType();

    public function setSchemaToDisplay($schema);

    public function getSchemaToDisplay();

    public function renderField($field);

    public function renderNodeHeader($obj, $params, $more_attr = null, $has_child = true);

    public function renderNodeFooter($obj, $params);

    public function renderAssociationHeader($obj, $params, $assoc_name, $closed_tags = false);

    public function renderAssociationFooter($obj, $params, $assoc_name);

    public function overrideContent($content);

    public function renderErrorsHeader();

    public function renderErrorsFooter();

    public function renderErrors($message, $code = null);

    public function setLanguages($languages);

    public function renderAssociationWrapperHeader();

    public function renderAssociationWrapperFooter();
}
