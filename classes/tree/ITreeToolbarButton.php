<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
interface ITreeToolbarButtonCore
{
    public function __toString();

    public function setAttribute($name, $value);

    public function getAttribute($name);

    public function setAttributes($value);

    public function getAttributes();

    public function setClass($value);

    public function getClass();

    public function setContext($value);

    public function getContext();

    public function setId($value);

    public function getId();

    public function setLabel($value);

    public function getLabel();

    public function setName($value);

    public function getName();

    public function setTemplate($value);

    public function getTemplate();

    public function setTemplateDirectory($value);

    public function getTemplateDirectory();

    public function hasAttribute($name);

    public function render();
}
