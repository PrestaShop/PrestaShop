<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
interface ITreeToolbarCore
{
    public function __toString();

    public function setActions($value);

    public function getActions();

    public function setContext($value);

    public function getContext();

    public function setData($value);

    public function getData();

    public function setTemplate($value);

    public function getTemplate();

    public function setTemplateDirectory($value);

    public function getTemplateDirectory();

    public function addAction($action);

    public function removeActions();

    public function render();
}
