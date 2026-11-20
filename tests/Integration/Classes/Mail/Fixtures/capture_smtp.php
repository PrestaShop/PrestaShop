<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
if ($argc !== 3) {
    exit(2);
}

$listener = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
if ($listener === false) {
    fwrite(STDERR, $errstr . "\n");
    exit(3);
}

$address = stream_socket_get_name($listener, false);
$port = (int) substr(strrchr($address, ':'), 1);
file_put_contents($argv[1], (string) $port);

$client = @stream_socket_accept($listener, 15);
if ($client === false) {
    fclose($listener);
    exit(4);
}
stream_set_timeout($client, 15);
fwrite($client, "220 localhost ESMTP test sink\r\n");
$capturing = false;
$message = '';
while (($line = fgets($client)) !== false) {
    $command = rtrim($line, "\r\n");
    if ($capturing) {
        if ($command === '.') {
            file_put_contents($argv[2], $message);
            $capturing = false;
            fwrite($client, "250 2.0.0 accepted\r\n");
        } else {
            $message .= str_starts_with($line, '..') ? substr($line, 1) : $line;
        }
        continue;
    }
    if (preg_match('/^EHLO\b/i', $command)) {
        fwrite($client, "250-localhost\r\n250 8BITMIME\r\n");
    } elseif (preg_match('/^HELO\b|^MAIL FROM:|^RCPT TO:|^RSET\b|^NOOP\b/i', $command)) {
        fwrite($client, "250 OK\r\n");
    } elseif (strcasecmp($command, 'DATA') === 0) {
        $capturing = true;
        fwrite($client, "354 End data with <CR><LF>.<CR><LF>\r\n");
    } elseif (strcasecmp($command, 'QUIT') === 0) {
        fwrite($client, "221 Bye\r\n");
        break;
    } else {
        fwrite($client, "500 Unsupported command\r\n");
    }
}
fclose($client);
fclose($listener);
