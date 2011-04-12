<?php

	class fianet_user_xml
	{
		var $type;
		//Qualité des clients par défaut
		var $qualite = 2;
		var $titre;
		var $nom;
		var $prenom;
		var $societe;
		var $telhome;
		var $teloffice;
		var $telmobile;
		var $telfax;
		var $email;
		var $site_conso = null;
		
		function set_quality_professional()
		{
			$this->qualite = 1;
		}
		
		function set_quality_nonprofessional()
		{
			$this->qualite = 2;
		}
		
		function get_xml()
		{	
			$xml = '';
			$xml .= "\t" . '<utilisateur type="'.$this->type.'" qualite="'.$this->qualite.'">' . "\n";
			if ($this->titre != '')
			{
				if ($this->titre == 'f')
				{
					$this->titre = 'mme';
				}
				$xml .= "\t\t" . '<nom titre="'.$this->titre.'">'.clean_invalid_char($this->nom).'</nom>' . "\n";
			}
			else
			{
				$xml .= "\t\t" . '<nom>'.clean_invalid_char($this->nom).'</nom>' . "\n";
			}
			$xml .= "\t\t" . '<prenom>'.clean_invalid_char($this->prenom).'</prenom>' . "\n";
			if ($this->societe != '')
			{
				$xml .= "\t\t" . '<societe>'.clean_invalid_char($this->societe).'</societe>' . "\n";
			}
			if ($this->telhome != '')
			{
				$xml .= "\t\t" . '<telhome>'.clean_invalid_char($this->telhome).'</telhome>' . "\n";
			}
			if ($this->teloffice != '')
			{
				$xml .= "\t\t" . '<teloffice>'.clean_invalid_char($this->teloffice).'</teloffice>' . "\n";
			}
			if ($this->telmobile != '')
			{
				$xml .= "\t\t" . '<telmobile>'.clean_invalid_char($this->telmobile).'</telmobile>' . "\n";
			}
			if ($this->telfax != '')
			{
				$xml .= "\t\t" . '<telfax>'.clean_invalid_char($this->telfax).'</telfax>' . "\n";
			}
			if ($this->email != '')
			{
				$xml .= "\t\t" . '<email>'.clean_invalid_char($this->email).'</email>' . "\n";
			}
			if ($this->site_conso != null)
			{
				$xml .= $this->site_conso->get_xml();
			}
			$xml .= "\t" . '</utilisateur>' . "\n";
			
			return ($xml);
		}
		
	}

