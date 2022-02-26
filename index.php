
function fixed_gethostbyname ($host) {
    // Try the lookup as normal...
    $ip = gethostbyname($host);
    // ...but if it fails, FALSE is returned instead of the unresolved host
    if ($ip != $host) { return $ip; } else return false;
}
function verifyemail() {
        global $domain, $username, $email, $HTTP_HOST;
        $domainexists = fixed_gethostbyname($domain);
        $mxexists = checkdnsrr("$domain.", 'MX');
        if (!$domainexists && !$mxexists) return false;
        else if (!$mxexists) return false;
        else {
                if (getmxrr($domain, $MXHost))  {
                        $ConnectAddress = $MXHost[0];
                } else {
                        $ConnectAddress = $domain;
                }
                $Connect = @fsockopen ( $ConnectAddress, 25 );
                if ($Connect) {
                        if (ereg("^220", $Out = fgets($Connect, 1024))) {
                                fputs ($Connect, "HELO $HTTP_HOST\r\n");
                                $Out = fgets ( $Connect, 1024 );
                                fputs ($Connect, "MAIL FROM: <{$email}>\r\n");
                                $From = fgets ( $Connect, 1024 );
                                fputs ($Connect, "RCPT TO: <{$email}>\r\n");
                                $To = fgets ($Connect, 1024);
                                fputs ($Connect, "QUIT\r\n");
                                fclose($Connect);
                                if (!ereg ("^250", $From) || !ereg ( "^250", $To )) {
                                        return false;
                                }
                        } else {
                                return false;
                        }
                } else {
                        return false;
                }
        }
        return true;
}
