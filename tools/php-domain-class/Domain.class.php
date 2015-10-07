<?php
/**
 * PHP Domain Class with Effective TLD Functionality.
 *
 * Copyright (C) 2007 Toby Inkster
 *
 * This file is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published
 * by the Free Software Foundation; either version 2, or (at your
 * option) any later version.
 *
 * This file is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this software; see the file COPYING. If not, write to
 * the Free Software Foundation, Inc., 59 Temple Place - Suite 330,
 * Boston, MA 02111-1307, USA.
 *
 * @author Toby Inkster
 * @copyright Copyright (C) 2007 Toby Inkster
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public Licence
 */

/**
 * URL of Effective TLD List
 */
define('EFFECTIVE_TLD_LIST', 'http://lxr.mozilla.org/mozilla/source/netwerk/dns/src/effective_tld_names.dat?raw=1');

/**
 * Filename to save Effective TLD List
 */
define('EFFECTIVE_TLD_FILENAME', 'effective_tld_names.dat');

/**
 * Add PrestaShop cache Folder
 */
define('CACHE_TLD_FILENAME', _PS_CACHE_DIR_.'php-domain-class/'.EFFECTIVE_TLD_FILENAME);

/**
 * Class for operating on Internet domain names.
 */
class Domain
{
	private $components	= null;
	private $domain		= null;
	private $etld		= null;
	private static $re_valid_dom	= '/^([a-z0-9][a-z0-9-]*[a-z0-9]\.){2,}$/';
	private static $re_valid_ip		= '/^([0-9]{1,3}\.){3}[0-9]{1,3}$/';
	private static $etld_data		= array();

	/**
	 * Create a Domain object.
	 *
	 * @param string $string The domain as a string.
	 * @return Domain Constructed Domain object.
	 * @throws Exception
	 */
	public function __construct ($string)
	{
		/* The canonical form of a domain is 'a.b.c.d' */
		if (!preg_match('/\.$/', $string))
		{
			$string .= '.';
		}
		$string = strtolower($string);

		if (!preg_match(self::$re_valid_dom, $string))
		{
			throw new Exception('Domain name is not valid: '.$string);
		}

		$this->domain = $string;
		$this->components = array_reverse(explode('.', $string));
		array_shift($this->components);

		return $this;
	}

	/**
	 * Factory to create a Domain object from an IP address.
	 *
	 * Uses a reverse DNS lookup to find an host name associated
	 * with the IP address.
	 *
	 * @param string $ip IP Address as dotted decimal.
	 * @return Domain Constructed Domain object.
	 * @throws Exception
	 */
	public static function from_ip ($ip)
	{
		if (!preg_match(self::$re_valid_ip, $ip))
		{
			throw new PrestaShopException('Invalid IP address: '.$ip);
		}

		$parts = explode('.', $ip);
		foreach ($parts as $p)
		{
			if ($p>255)
			{
				throw new PrestaShopException('Invalid IP address: '.$ip);
			}
		}

		return new Domain(gethostbyaddr($ip));
	}

	/**
	 * Factory to create a Domain object from a URL.
	 *
	 * Uses a parse_url to extract domain name or IP address from
	 * the URL and constructs a Domain object from that.
	 *
	 * @param string $url URL.
	 * @return Domain Constructed Domain object.
	 * @throws Exception
	 */
	public static function from_url ($url)
	{
		$URL = parse_url($url);

		if (!isset($URL['host']))
		{
			throw new PrestaShopException('Invalid URL: '.$url);
		}

		if (preg_match(self::$re_valid_ip, $URL['host']))
		{
			return self::from_ip($URL['host']);
		}

		return new Domain($URL['host']);
	}

	/**
	 * Downloads and saves ETLD data if it's not there.
	 *
	 * @return string File name with full path.
	 */
	private static function etld_fetch_list ()
	{
		if (!(is_file(CACHE_TLD_FILENAME)
			&& filesize(CACHE_TLD_FILENAME) > 0
            && ((time() - filemtime(CACHE_TLD_FILENAME)) < 2592000)
            ))
		{
			if (!@file_put_contents(CACHE_TLD_FILENAME, Tools::file_get_contents(EFFECTIVE_TLD_LIST))){
				throw new PrestaShopException('Could not write : '.CACHE_TLD_FILENAME);
			}
		}
		return CACHE_TLD_FILENAME;
	}

	/**
	 * Reads ETLD data into memory.
	 *
	 * @return boolean Did any work need to be done?
	 */
	private static function etld_read_list ()
	{
		if (count(self::$etld_data))
		{
			return false;
		}
		$data = file(self::etld_fetch_list());
		if (is_array($data))
		{
			foreach ($data as $line)
			{
				/* Ignore blank lines and comments. */
				if (preg_match('#(^//)|(^\s*$)#', $line))
					continue;

				self::$etld_data[] = preg_replace('/[\r\n]/', '', $line);
			}
		}
		else
		{
			return false;
		}
		return true;
	}

	/**
	 * Finds the ETLD for the Domain object.
	 *
	 * @return string Effective TLD.
	 */
	public function get_etld ()
	{
		if (!strlen($this->etld))
		{
			self::etld_read_list();
			$LIST = self::$etld_data;

			$d = '';
			$E = '';

			foreach ($this->components as $c)
			{
				$a = "*.{$d}";
				$d = "{$c}.{$d}";
				$A = preg_replace('/\.$/', '', $a);
				$D = preg_replace('/\.$/', '', $d);

				if (in_array("!{$D}", $LIST))
				{
					$E = $D;
					$E = preg_replace('/^([^\.])*\./', '', $E);
					break;
				}
				elseif (in_array($D, $LIST))
				{
					$E = $D;
				}
				elseif (in_array($A, $LIST))
				{
					$E = $D;
				}
			}
			$this->etld = "{$E}.";
		}
		return $this->etld;
	}

	/**
	 * Gets a specified number of URL components.
	 *
	 * @param int $number How many components to get?
	 * @param bool $etld Treat ETLD as one component?
	 * @return string Domain name.
	 */
	public function get_components ($number, $etld=false)
	{
		if ($number<1)
		{
			return '';
		}
		$retval = '';
		$parts  = $this->components;
		if ($etld)
		{
			$retval = $this->get_etld();
			$number--;
			$n = substr_count($retval, '.');
			for ($i=0; $i<$n; $i++)
				array_shift($parts);
		}

		for ($i=0; $i<$number; $i++)
		{
			$retval = array_shift($parts) . '.' . $retval;
		}

		return $retval;
	}

	/**
	 * Gets the domain, excluding parts normally thought of as subdomains.
	 *
	 * @return string Domain.
	 */
	public function get_reg_domain ()
	{
		return $this->get_components(2, true);
	}

	/**
	 * Overloaded __tostring().
	 *
	 * A useful textual equivalent to the Domain object.
	 */
	public function __tostring ()
	{
		return $this->domain;
	}
}
?>
