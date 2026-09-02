<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

/**
 * Step 2 : display license form
 */
class InstallControllerHttpLicense extends InstallControllerHttp implements HttpConfigureInterface
{
    /**
     * {@inheritdoc}
     */
    public function processNextStep(): void
    {
        $this->session->licence_agrement = (bool) Tools::getValue('licence_agrement');
        $this->session->configuration_agrement = Tools::getValue('configuration_agrement');
    }

    /**
     * {@inheritdoc}
     */
    public function validate(): bool
    {
        return (bool) $this->session->licence_agrement;
    }

    /**
     * {@inheritdoc}
     */
    public function display(): void
    {
        $this->displayContent('license');
    }
}
