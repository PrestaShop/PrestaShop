<?php
class HIPAY_MAPI_SEND_XML {

    /**
     * Envoi le flux XML et retourne la réponse du serveur
     *
     * un timeout trop bas
     * Une mauvaise url
     * un proxy mal configuré s'il existe
     *
     * peuvent engendrer une erreur de connexion
     *
     * @param string $xml
     * @return string
     */
    public static function sendXML($xml, $url = "") {
        $xml = self::prepare($xml);

        if ($url == "")
            $turl = parse_url(HIPAY_GATEWAY_URL);
        else 
			$turl = parse_url($url);
			
        if (!isset($turl['path']))
            $turl['path'] = '/';

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_TIMEOUT, HIPAY_MAPI_CURL_TIMEOUT);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_USERAGENT, "HIPAY");
        curl_setopt($curl, CURLOPT_URL, $turl['scheme'].'://'.$turl['host'].$turl['path']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'xml='.urlencode($xml));

        if(HIPAY_MAPI_CURL_PROXY_ON === true)
        {
            curl_setopt($curl, CURLOPT_PROXY, HIPAY_MAPI_CURL_PROXY);
            curl_setopt($curl, CURLOPT_PROXYPORT, HIPAY_MAPI_CURL_PROXYPORT);
        }

        if(HIPAY_MAPI_CURL_LOG_ON === true)
        {
            $errorFileLog = fopen(HIPAY_MAPI_CURL_LOGFILE, "a+");
            curl_setopt($curl, CURLOPT_VERBOSE, true);
            curl_setopt($curl, CURLOPT_STDERR, $errorFileLog);
        }

        curl_setopt($curl, CURLOPT_HEADER, 0);

        ob_start();
        if (curl_exec($curl) !== true)
        {
            $output = $turl['scheme'].'://'.$turl['host'].$turl['path'].' is not reachable';
            $output .= '<br />Network problem ? Verify your proxy configuration in mapi_defs.php';
        }
        else 
			$output = ob_get_contents();
			
        ob_end_clean();
        curl_close($curl);
		
        if(HIPAY_MAPI_CURL_LOG_ON === true)
        {
            fclose($errorFileLog);
        }
		
        return $output;
    }

    /**
     * Prépare le flux XML
     *
     * @param string $xml
     * @return string
     */
    public static function prepare($xml) {
        $cleanXML = '';
        $xml = trim($xml);
        $md5 = hash('md5', $xml);
        $cleanXML = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
        $cleanXML .= "<mapi>\n";
        $cleanXML .= "<mapiversion>".MAPI_VERSION."</mapiversion>\n";
        $cleanXML .= '<md5content>'.$md5."</md5content>\n";
        $cleanXML .= $xml;
        $cleanXML .= "\n</mapi>\n";
		
        return trim($cleanXML);
    }
}