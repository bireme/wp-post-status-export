<?php

if ( !function_exists( 'parse_stats' ) ) {
    /**
     * Parse XML to Array
     *
     * @param string $url
     * @return boolean|array
     */
    function parse_stats($url=''){
        if ( empty($url) ) {
            return false;
        }

        /* load simpleXML object */
        $xml    = @file_get_contents($url);
        $xml    = simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);
        $json   = json_encode($xml);
        $result = json_decode($json,true);

        return $result;
    }
}

?>
