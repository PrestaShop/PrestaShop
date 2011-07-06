<?php

/*

BUG FIXED : PSCFI-1480

There is problem with server using apache with php under CGI MODE.
The authentification isn't created in CGI MOD and this module won't work.

To fix it, the .htaccess will create this missing variable

Here is the .htaccess detailed :

<IfModule mod_rewrite.c>
RewriteEngine on
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteRule ^(.*) - [E=HTTP_AUTHORIZATION:%1]
</IfModule>

*/
?>
