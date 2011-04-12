<?php
/*
** PHP implementation of the RSA Data Security, Inc. MD5 Message
** Digest Algorithm, as defined in RFC 1321.
*
** Version 1.1
** Copyright 2004 Marcus Campbell
** http://www.tecknik.net/md5/
*
** This code is available under the GNU Lesser General Public License:
** http://www.gnu.org/licenses/lgpl.txt
*
** Based on the JavaScript implementation by Paul Johnston
** http://pajhome.org.uk/
*/
Class HashMD5 {
    var $a;
    var $b;
    var $c;
    var $d;
    
    var $Save_a;
    var $Save_b;
    var $Save_c;
    var $Save_d;
    
    function HashMD5() { 
        /*
        $a =  1732584190;    
        $b = -271733879;
        $c = -1732584194;
        $d =  271733878; */
        $this->a = -279229019;
        $this->b = -1875190530;
        $this->c = 1737040641;
        $this->d = 315143286; 
        
        $this->Save_a = $this->a; 
        $this->Save_b = $this->b; 
        $this->Save_c = $this->c; 
        $this->Save_d = $this->d; 
    }   
        
    function FlushKey($k0, $k1, $k2, $k3) {
        $this->a = $k0;
        $this->b = $k1;
        $this->c = $k2;
        $this->d = $k3; 

        $this->Save_a = $this->a; 
        $this->Save_b = $this->b; 
        $this->Save_c = $this->c; 
        $this->Save_d = $this->d; 
    }

    function Init() {
        $this->a = $this->Save_a; 
        $this->b = $this->Save_b; 
        $this->c = $this->Save_c; 
        $this->d = $this->Save_d; 
    }

    function rhex($num) {
        $hex_chr = "0123456789abcdef";
        $str = "";
        for($j = 0; $j <= 3; $j++)
            $str .= substr($hex_chr, ($num >> ($j * 8 + 4)) & 0x0F, 1) . substr($hex_chr, ($num >> ($j * 8)) & 0x0F, 1);
        return $str;
    }
    
    function str2blks_MD5($str) {
        $nblk = ((strlen($str) + 8) >> 6) + 1;
        for($i = 0; $i < $nblk * 16; $i++) $blks[$i] = 0;
        for($i = 0; $i < strlen($str); $i++)
            $blks[$i >> 2] |= ord(substr($str, $i, 1)) << (($i % 4) * 8);
        $blks[$i >> 2] |= 0x80 << (($i % 4) * 8);
        $blks[$nblk * 16 - 2] = strlen($str) * 8;
        return $blks;
    }
    
    function add($x, $y) {
        $lsw = ($x & 0xFFFF) + ($y & 0xFFFF);
        $msw = ($x >> 16) + ($y >> 16) + ($lsw >> 16);
        return ($msw << 16) | ($lsw & 0xFFFF);
    }
    
    function rol($num, $cnt) {
        return ($num << $cnt) | $this->zeroFill($num, 32 - $cnt);
    }
    
    function zeroFill($a, $b) {
        $bin = decbin($a);
        if (strlen($bin) < $b) $bin = 0;
        else $bin = substr($bin, 0, strlen($bin) - $b);
        for ($i=0; $i < $b; $i++) {
            $bin = "0".$bin;
        }
        return bindec($bin);
    }
    
    function cmn($q, $a, $b, $x, $s, $t) {
        return $this->add($this->rol($this->add($this->add($a, $q), $this->add($x, $t)), $s), $b);
    }
    
    function ff($a, $b, $c, $d, $x, $s, $t) {
        return $this->cmn(($b & $c) | ((~$b) & $d), $a, $b, $x, $s, $t);
    }
    
    function gg($a, $b, $c, $d, $x, $s, $t) {
        return $this->cmn(($b & $d) | ($c & (~$d)), $a, $b, $x, $s, $t);
    }
    
    function hh($a, $b, $c, $d, $x, $s, $t) {
        return $this->cmn($b ^ $c ^ $d, $a, $b, $x, $s, $t);
    }
    
    function ii($a, $b, $c, $d, $x, $s, $t) {
        return $this->cmn($c ^ ($b | (~$d)), $a, $b, $x, $s, $t);
    }
    
    function hash($str) {
      $x = $this->str2blks_MD5($str);
      
      $this->Init();
      
      for($i = 0; $i < sizeof($x); $i += 16) {
        $olda = $this->a;
        $oldb = $this->b;
        $oldc = $this->c;
        $oldd = $this->d;
    
        $this->a = $this->ff($this->a, $this->b, $this->c, $this->d, $x[$i+ 0], 7 , -680876936);
        $this->d = $this->ff($this->d, $this->a, $this->b, $this->c, $x[$i+ 1], 12, -389564586);
        $this->c = $this->ff($this->c, $this->d, $this->a, $this->b, $x[$i+ 2], 17,  606105819);
        $this->b = $this->ff($this->b, $this->c, $this->d, $this->a, $x[$i+ 3], 22, -1044525330);
        $this->a = $this->ff($this->a, $this->b, $this->c, $this->d, $x[$i+ 4], 7 , -176418897);
        $this->d = $this->ff($this->d, $this->a, $this->b, $this->c, $x[$i+ 5], 12,  1200080426);
        $this->c = $this->ff($this->c, $this->d, $this->a, $this->b, $x[$i+ 6], 17, -1473231341);
        $this->b = $this->ff($this->b, $this->c, $this->d, $this->a, $x[$i+ 7], 22, -45705983);
        $this->a = $this->ff($this->a, $this->b, $this->c, $this->d, $x[$i+ 8], 7 ,  1770035416);
        $this->d = $this->ff($this->d, $this->a, $this->b, $this->c, $x[$i+ 9], 12, -1958414417);
        $this->c = $this->ff($this->c, $this->d, $this->a, $this->b, $x[$i+10], 17, -42063);
        $this->b = $this->ff($this->b, $this->c, $this->d, $this->a, $x[$i+11], 22, -1990404162);
        $this->a = $this->ff($this->a, $this->b, $this->c, $this->d, $x[$i+12], 7 ,  1804603682);
        $this->d = $this->ff($this->d, $this->a, $this->b, $this->c, $x[$i+13], 12, -40341101);
        $this->c = $this->ff($this->c, $this->d, $this->a, $this->b, $x[$i+14], 17, -1502002290);
        $this->b = $this->ff($this->b, $this->c, $this->d, $this->a, $x[$i+15], 22,  1236535329);
    
        $this->a = $this->gg($this->a, $this->b, $this->c, $this->d, $x[$i+ 1], 5 , -165796510);
        $this->d = $this->gg($this->d, $this->a, $this->b, $this->c, $x[$i+ 6], 9 , -1069501632);
        $this->c = $this->gg($this->c, $this->d, $this->a, $this->b, $x[$i+11], 14,  643717713);
        $this->b = $this->gg($this->b, $this->c, $this->d, $this->a, $x[$i+ 0], 20, -373897302);
        $this->a = $this->gg($this->a, $this->b, $this->c, $this->d, $x[$i+ 5], 5 , -701558691);
        $this->d = $this->gg($this->d, $this->a, $this->b, $this->c, $x[$i+10], 9 ,  38016083);
        $this->c = $this->gg($this->c, $this->d, $this->a, $this->b, $x[$i+15], 14, -660478335);
        $this->b = $this->gg($this->b, $this->c, $this->d, $this->a, $x[$i+ 4], 20, -405537848);
        $this->a = $this->gg($this->a, $this->b, $this->c, $this->d, $x[$i+ 9], 5 ,  568446438);
        $this->d = $this->gg($this->d, $this->a, $this->b, $this->c, $x[$i+14], 9 , -1019803690);
        $this->c = $this->gg($this->c, $this->d, $this->a, $this->b, $x[$i+ 3], 14, -187363961);
        $this->b = $this->gg($this->b, $this->c, $this->d, $this->a, $x[$i+ 8], 20,  1163531501);
        $this->a = $this->gg($this->a, $this->b, $this->c, $this->d, $x[$i+13], 5 , -1444681467);
        $this->d = $this->gg($this->d, $this->a, $this->b, $this->c, $x[$i+ 2], 9 , -51403784);
        $this->c = $this->gg($this->c, $this->d, $this->a, $this->b, $x[$i+ 7], 14,  1735328473);
        $this->b = $this->gg($this->b, $this->c, $this->d, $this->a, $x[$i+12], 20, -1926607734);
    
        $this->a = $this->hh($this->a, $this->b, $this->c, $this->d, $x[$i+ 5], 4 , -378558);
        $this->d = $this->hh($this->d, $this->a, $this->b, $this->c, $x[$i+ 8], 11, -2022574463);
        $this->c = $this->hh($this->c, $this->d, $this->a, $this->b, $x[$i+11], 16,  1839030562);
        $this->b = $this->hh($this->b, $this->c, $this->d, $this->a, $x[$i+14], 23, -35309556);
        $this->a = $this->hh($this->a, $this->b, $this->c, $this->d, $x[$i+ 1], 4 , -1530992060);
        $this->d = $this->hh($this->d, $this->a, $this->b, $this->c, $x[$i+ 4], 11,  1272893353);
        $this->c = $this->hh($this->c, $this->d, $this->a, $this->b, $x[$i+ 7], 16, -155497632);
        $this->b = $this->hh($this->b, $this->c, $this->d, $this->a, $x[$i+10], 23, -1094730640);
        $this->a = $this->hh($this->a, $this->b, $this->c, $this->d, $x[$i+13], 4 ,  681279174);
        $this->d = $this->hh($this->d, $this->a, $this->b, $this->c, $x[$i+ 0], 11, -358537222);
        $this->c = $this->hh($this->c, $this->d, $this->a, $this->b, $x[$i+ 3], 16, -722521979);
        $this->b = $this->hh($this->b, $this->c, $this->d, $this->a, $x[$i+ 6], 23,  76029189);
        $this->a = $this->hh($this->a, $this->b, $this->c, $this->d, $x[$i+ 9], 4 , -640364487);
        $this->d = $this->hh($this->d, $this->a, $this->b, $this->c, $x[$i+12], 11, -421815835);
        $this->c = $this->hh($this->c, $this->d, $this->a, $this->b, $x[$i+15], 16,  530742520);
        $this->b = $this->hh($this->b, $this->c, $this->d, $this->a, $x[$i+ 2], 23, -995338651);
    
        $this->a = $this->ii($this->a, $this->b, $this->c, $this->d, $x[$i+ 0], 6 , -198630844);
        $this->d = $this->ii($this->d, $this->a, $this->b, $this->c, $x[$i+ 7], 10,  1126891415);
        $this->c = $this->ii($this->c, $this->d, $this->a, $this->b, $x[$i+14], 15, -1416354905);
        $this->b = $this->ii($this->b, $this->c, $this->d, $this->a, $x[$i+ 5], 21, -57434055);
        $this->a = $this->ii($this->a, $this->b, $this->c, $this->d, $x[$i+12], 6 ,  1700485571);
        $this->d = $this->ii($this->d, $this->a, $this->b, $this->c, $x[$i+ 3], 10, -1894986606);
        $this->c = $this->ii($this->c, $this->d, $this->a, $this->b, $x[$i+10], 15, -1051523);
        $this->b = $this->ii($this->b, $this->c, $this->d, $this->a, $x[$i+ 1], 21, -2054922799);
        $this->a = $this->ii($this->a, $this->b, $this->c, $this->d, $x[$i+ 8], 6 ,  1873313359);
        $this->d = $this->ii($this->d, $this->a, $this->b, $this->c, $x[$i+15], 10, -30611744);
        $this->c = $this->ii($this->c, $this->d, $this->a, $this->b, $x[$i+ 6], 15, -1560198380);
        $this->b = $this->ii($this->b, $this->c, $this->d, $this->a, $x[$i+13], 21,  1309151649);
        $this->a = $this->ii($this->a, $this->b, $this->c, $this->d, $x[$i+ 4], 6 , -145523070);
        $this->d = $this->ii($this->d, $this->a, $this->b, $this->c, $x[$i+11], 10, -1120210379);
        $this->c = $this->ii($this->c, $this->d, $this->a, $this->b, $x[$i+ 2], 15,  718787259);
        $this->b = $this->ii($this->b, $this->c, $this->d, $this->a, $x[$i+ 9], 21, -343485551);
    
        $this->a = $this->add($this->a, $olda);
        $this->b = $this->add($this->b, $oldb);
        $this->c = $this->add($this->c, $oldc);
        $this->d = $this->add($this->d, $oldd);
        }
        return $this->rhex($this->a) . $this->rhex($this->b) . $this->rhex($this->c) . $this->rhex($this->d);
    }
}
Class EncodingKey { 
    var $clMD5;
    
    function EncodingKey() {
        $this->clMD5 = new HashMD5();
    }
    
    function giveHashCode($pkey, $second, $email, $refid, $montant, $nom) {
     
        $modulo = $second % 4;
        
        switch($modulo) {
            case 0:
                $select = $montant;
            break;    
            case 1:
                $select = $email;
            break;    
            case 2:
                $select = $refid;
            break;    
            case 3:
                $select = $nom;
            break;    
            default:
            break;    
        }
        
        return $this->clMD5->hash($pkey.$refid.$select);
    
    }

    function giveHashCode2($pkey, $second, $email, $refid, $montant, $nom) {
        $modulo = $second % 4;
        
$montant = sprintf("%01.2f",$montant);

        switch($modulo) {
            case 0:
                $select = $montant;
            break;    
            case 1:
                $select = $email;
            break;    
            case 2:
                $select = $refid;
            break;    
            case 3:
                $select = $nom;
            break;    
            default:
            break;    
        }
/*
            $fp = fopen('/var/www/fianet/test_greg.txt','ab');
    fwrite($fp,"second=".$second."\n");
    fwrite($fp,"montant=".$montant."\n");
    fwrite($fp,"email=".$email."\n");
    fwrite($fp,"refid=".$refid."\n");
    fwrite($fp,"nom=".$nom."\n");
    fclose($fp);
*/

        return $this->clMD5->hash($pkey.$refid.$montant.$email.$select);
    
    }
}
