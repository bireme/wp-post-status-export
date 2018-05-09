<?php
/**
 * Feed generator template.
 */

$post_type = ( $_GET['post_type'] ) ? $_GET['post_type'] : 'post';
$format = ( $_GET['format'] ) ? $_GET['format'] : 'xml';

$count_posts = wp_count_posts($post_type);

$publish_posts = $count_posts->publish;
$draft_posts   = $count_posts->draft;
$pending_posts = $count_posts->pending;

$total = $publish_posts + $draft_posts + $pending_posts;

$stats = array(
    'total' => $total,
    'status' => array(
        'publish' => $publish_posts,
        'draft'   => $draft_posts,
        'pending' => $pending_posts
    )
);

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
</stats>
<?php
    rss_enclosure();
    do_action('rss2_item');
    wp_reset_postdata();
?>
<?php } ?>
