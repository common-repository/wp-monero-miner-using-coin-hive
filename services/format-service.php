<?php

class FormatService
{
    public function formatHashes($hashes)
    {
        return number_format($hashes, 0, '.', ' ');
    }
}

?>