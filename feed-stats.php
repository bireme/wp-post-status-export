<?php
/**
 * Output generator template.
 */

$post_type = ( $_GET['post_type'] ) ? $_GET['post_type'] : 'post';
$format = ( $_GET['format'] ) ? $_GET['format'] : 'xml';

$count_posts = wp_count_posts($post_type);

$publish_posts = $count_posts->publish;
$draft_posts   = $count_posts->draft;
$pending_posts = $count_posts->pending;
$trash_posts   = $count_posts->trash;

$total = $publish_posts + $draft_posts + $pending_posts + $trash_posts;

$stats = array(
    'total'  => $total,
    'status' => array(
        'publish' => $publish_posts,
        'draft'   => $draft_posts,
        'pending' => $pending_posts,
        'trash'   => $trash_posts
    )
);

if ( $_GET['tax'] ) {
    $args = array(
        'type'         => 'post',
        'child_of'     => 0,
        'parent'       => '',
        'orderby'      => 'name',
        'order'        => 'ASC',
        'hide_empty'   => 1,
        'hierarchical' => 1,
        'exclude'      => '',
        'include'      => '',
        'number'       => '',
        'taxonomy'     => $_GET['tax'],
        'pad_counts'   => false 
    ); 
    
    $categories = get_categories( $args );

    if ( $categories['errors'] ) {
        die(utf8_decode($categories['errors']['invalid_taxonomy'][0]));
    }

    $stats['taxonomy'] = array();

    foreach ( $categories as $cat ) {
        $args = array();
        $args['posts_per_page'] = -1;
        $args['post_type']      = $post_type;
        $args['post_status']    = array( 'publish', 'pending', 'draft', 'trash' );
        $args['tax_query']      = array(
            array(
                'taxonomy' => $_GET['tax'],
                'field' => 'term_id',
                'terms' => $cat->term_id
            )
        );

        $posts = get_posts($args);
        $post_status = wp_list_pluck( $posts, 'post_status');
        $tax_stats = array_count_values($post_status);

        arsort($tax_stats);

        $taxonomy = array(
            'name'   => $cat->name,
            'total'  => array_sum($tax_stats),
            'status' => $tax_stats
        );

        $stats['taxonomy'][] = $taxonomy;
    }
}

if ( 'json' == $format ) {
    die(json_encode($stats));
} else {
header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>';
?>

<stats>
    <total><?php echo $total; ?></total>
    <status>
        <?php foreach ($stats['status'] as $key => $value) : ?>
        <field name="<?php echo $key; ?>"><?php echo $value; ?></field>
        <?php endforeach; ?>
    </status>
    <?php if ( $stats['taxonomy'] ) : ?>
    <taxonomy>
        <?php foreach ($stats['taxonomy'] as $tax) : ?>
        <item>
            <name><![CDATA[<?php echo $tax['name']; ?>]]></name>
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
    <?php endif; ?>
</stats>

<?php
    rss_enclosure();
    do_action('rss2_item');
    wp_reset_postdata();
?>

<?php } ?>
