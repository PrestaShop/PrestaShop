<?php

function add_accounting_tab()
{
	$id_parent = add_new_tab(
		'AdminAccounting', 
		'en:Accounting|fr:Comptabilité|es:Accounting|de:Accounting|it:Accounting', 
		0, 
		true);

	add_new_tab(
		'AdminAccountingManagement',
		'en:Account Number Management|fr:Gestion des numéros de comptes|es:Account Number Management|de:Account Number Management|it:Account Number Management',
		$id_parent);

	add_new_tab(
		'AdminAccountingExport',
		'en:Export|fr:Export|es:Export|de:Export|it:Export',
		$id_parent);
}