<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
interface WebserviceSpecificManagementInterface
{
    public function setObjectOutput(WebserviceOutputBuilder $obj);

    public function getObjectOutput();

    public function setWsObject(WebserviceRequest $obj);

    public function getWsObject();

    public function manage();

    /**
     * This must be return a string with specific values as WebserviceRequest expects.
     *
     * @return string
     */
    public function getContent();
}
