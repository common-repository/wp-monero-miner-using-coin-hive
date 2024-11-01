<?php

class SiteService {

    public function get_current_host()
    {
        $sURL = site_url();
        $parts = parse_url($sURL);

        if (!$parts)
            return '';

        return $parts['host'];
    }

    public function get_current_scheme()
    {
        $sURL = site_url();
        $parts = parse_url($sURL);

        if (!$parts)
            return 'https';

        return $parts['scheme'];
    }
}

?>