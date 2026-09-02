{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

{extends file="helpers/list/list_header.tpl"}
	{block name="preTable"}
		{if $showHeaderAlert}
			<div class="alert alert-info">
				<p>{l s='Your online store needs to have a proper carrier registered in PrestaShop as soon as you start shipping your products. This means sending yours parcels using your local postal service, or having a contract with a private carrier which in turn will ship your parcels to your customers. In order to have PrestaShop suggest the most adequate carrier to your customers during their order checkout process, you need to register all the carriers with which you have chosen to work.' d='Admin.Shipping.Help'}</p>
				<p>{l s='If there is no existing module for your carrier, then you can register that carrier by hand using the information that it can provide you: shipping rates, regional zones, size and weight limits, etc. Click on the "%add_new_label%" button below to open the Carrier Wizard, which will help you register a new carrier in a few steps.' d='Admin.Shipping.Help' sprintf=['%add_new_label%' => {l s='Add new carrier' d='Admin.Shipping.Feature'}]}</p>
				<p>{l s='Note: DO NOT register a new carrier if there already exists a module for it! Using a module will be much faster and more accurate!' d='Admin.Shipping.Help'}</p>
			</div>
		{/if}
	{/block}
