<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
interface HttpConfigureInterface
{
    /**
     * Process form to go to next step
     */
    public function processNextStep();

    /**
     * Validate current step
     */
    public function validate();

    /**
     * Display current step view
     */
    public function display();
}
