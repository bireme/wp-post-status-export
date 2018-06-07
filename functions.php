<?php

if ( !function_exists( 'parse_stats' ) ) {
    /**
     * Integer comparison
     *
     * @param int $a
     * @param int $b
     * @return boolean
     */
    function intcmp($a, $b) {
        if ( array_key_exists('votes', $a) && array_key_exists('votes', $b) ) {
            return $a["votes"] - $b["votes"];
        } else {
            return $a["total"] - $b["total"];
        }
    }
}

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

if ( !function_exists( 'get_poll' ) ) {
    /**
     * Get poll metadata
     *
     * @param int $poll
     * @return array
     */
    function get_poll($poll){
        global $wpdb;
        $result = array();

        if ( $poll ) {
            $sql = $wpdb->prepare( "SELECT * FROM $wpdb->yop_poll_questions WHERE ID = '%d'", $poll );
            $result = $wpdb->get_results( $sql );
        }

        return $result;
    }
}

if ( !function_exists( 'get_poll_answers' ) ) {
    /**
     * Get poll answers
     *
     * @param int $poll
     * @return array
     */
    function get_poll_answers($poll){
        global $wpdb;
        $answers = array();

        if ( $poll ) {
            $sql = $wpdb->prepare( "SELECT * FROM $wpdb->yop_poll_answers WHERE poll_id = '%d' ORDER BY $wpdb->yop_poll_answers.ID", $poll );
            $answers = $wpdb->get_results( $sql );
        }

        return $answers;
    }
}

if ( !function_exists( 'print_taxonomy_stats' ) ) {
    /**
     * Print taxomony stats
     *
     * @param array $stats
     * @param string $taxonomy
     * @return XML
     */
    function print_taxonomy_stats($stats=array(), $taxonomy){
        if ( $stats && is_array($stats) ) : ?>
        <taxonomy>
            <?php foreach ($stats['taxonomy'] as $tax) : ?>
            <item>
                <name><![CDATA[<?php echo $tax['name']; ?>]]></name>
                <?php if ( 'decs' == $taxonomy ) : ?>
                <tree_id><?php echo $tax['tree_id']; ?></tree_id>
                <decs_id><?php echo $tax['decs_id']; ?></decs_id>
                <?php endif; ?>
                <total><?php echo $tax['total']; ?></total>
                <?php if ( $tax['status'] ) : ?>
                <status>
                    <?php foreach ($tax['status'] as $k => $v) : ?>
                    <field name="<?php echo $k; ?>"><?php echo $v; ?></field>
                    <?php endforeach; ?>
                </status>
                <?php endif; ?>
            </item>
            <?php endforeach; ?>
        </taxonomy>
        <?php endif;
    }
}

if ( !function_exists( 'print_poll_stats' ) ) {
    /**
     * Print poll stats
     *
     * @param array $stats
     * @return XML
     */
    function print_poll_stats($stats=array()){
        if ( $stats && is_array($stats) ) : ?>
        <poll>
            <name><![CDATA[<?php echo $stats['poll']['name']; ?>]]></name>
            <votes><?php echo $stats['poll']['votes']; ?></votes>
            <?php if ( $stats['poll']['answers'] ) : ?>
            <answers>
                <?php foreach ( $stats['poll']['answers'] as $answer ) : ?>
                <answer>
                    <name><![CDATA[<?php echo $answer['name']; ?>]]></name>
                    <votes><?php echo $answer['votes']; ?></votes>
                </answer>
                <?php endforeach; ?>
            </answers>
            <?php endif; ?>
            <?php if ( $stats['poll']['sofs'] ) : ?>
            <sofs>
                <?php foreach ( $stats['poll']['sofs'] as $sof ) : ?>
                <sof>
                    <title><![CDATA[<?php echo $sof['title']; ?>]]></title>
                    <link><?php echo $sof['link']; ?></link>
                    <votes><?php echo $sof['votes']; ?></votes>
                    <?php if ( $sof['answers'] ) : ?>
                    <answers>
                        <?php foreach ( $sof['answers'] as $answer ) : ?>
                        <answer>
                            <name><![CDATA[<?php echo $answer['name']; ?>]]></name>
                            <votes><?php echo $answer['votes']; ?></votes>
                        </answer>
                        <?php endforeach; ?>
                    </answers>
                    <?php endif; ?>
                </sof>
                <?php endforeach; ?>
            </sofs>
            <?php endif; ?>
        </poll>
        <?php endif;
    }
}

?>
