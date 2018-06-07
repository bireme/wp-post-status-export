<?php
/**
 * Output generator template.
 */

$post_type     = ( $_GET['post_type'] ) ? $_GET['post_type'] : 'post';
$format        = ( $_GET['format'] ) ? $_GET['format'] : 'xml';
$count         = ( $_GET['count'] ) ? $_GET['count'] : -1;

$count_posts   = wp_count_posts($post_type);

$publish_posts = $count_posts->publish;
$draft_posts   = $count_posts->draft;
$pending_posts = $count_posts->pending;
$trash_posts   = $count_posts->trash;

$total = $publish_posts + $draft_posts + $pending_posts + $trash_posts;

$stats = array(
    'total'  => $total,
    'status' => array(
        'publish' => (int) $publish_posts,
        'draft'   => (int) $draft_posts,
        'pending' => (int) $pending_posts,
        'trash'   => (int) $trash_posts
    )
);

$args = array(
    'post_type'      => $post_type,
    'post_status'    => array( 'publish', 'pending', 'draft', 'trash' ),
);

// initial_date and end_date parameters
if ( $_GET['initial_date'] && $_GET['end_date'] ) {
    $initial_date = getdate(strtotime($_GET['initial_date']));
    $end_date     = getdate(strtotime($_GET['end_date']));

    $args['posts_per_page'] = $count ;
    $args['date_query'] = array(
        array(
            'after' => array(
                'year'  => $initial_date['year'],
                'month' => $initial_date['mon'],
                'day'   => $initial_date['mday'],
            ),
            'before' => array(
                'year'  => $end_date['year'],
                'month' => $end_date['mon'],
                'day'   => $end_date['mday'],
            ),
            'inclusive' => true,
        ),
    );

    $posts = get_posts($args);
    $post_status = wp_list_pluck( $posts, 'post_status');
    $posts_per_status = array_count_values($post_status);
    $total = count($posts);

    $publish_posts = ( $posts_per_status['publish'] ) ? $posts_per_status['publish'] : 0;
    $draft_posts   = ( $posts_per_status['draft'] ) ? $posts_per_status['draft'] : 0;
    $pending_posts = ( $posts_per_status['pending'] ) ? $posts_per_status['pending'] : 0;
    $trash_posts   = ( $posts_per_status['trash'] ) ? $posts_per_status['trash'] : 0;

    $stats = array(
        'total'  => $total,
        'status' => array(
            'publish' => $publish_posts,
            'draft'   => $draft_posts,
            'pending' => $pending_posts,
            'trash'   => $trash_posts
        )
    );
}

// poll parameter
if ( $_GET['poll'] ) {
    $poll = $_GET['poll'];
    $args['posts_per_page'] = -1 ;
    $args['meta_key'] = 'yop_poll_'.$poll ;

    $data    = get_poll($poll);
    $votes   = yop_poll_ret_poll_by_votes_desc(array($poll));

    $answers = $poll_answers = get_poll_answers($poll);
    $answers = array_map("get_object_vars", $answers);
    $answers = array_map(function ($arr) {
        $keys = array( 'answer' => '', 'votes' => '' );
        return array_intersect_key($arr, $keys);
    }, $answers);
    $answers = str_replace('"answer":', '"name":', json_encode($answers));
    $answers = json_decode($answers, true);

    $stats['poll'] = array(
        'name'    => $data[0]->question,
        'votes'   => $votes[0]['poll_total_votes'],
        'answers' => $answers
    );

    $sofs = array();

    $posts = get_posts($args);

    foreach ($posts as $post) {
        $answers = array();
        $meta = get_post_meta($post->ID, 'yop_poll_'.$poll);

        foreach ($poll_answers as $answer) {
            $key = 'answer_'.$answer->ID;

            if ( array_key_exists($key, $meta[0]) ) {
                $answers[] = array(
                    'name'  => $answer->answer,
                    'votes' => $meta[0][$key]['votes']
                );
            }
        }

        $sof = array(
            'title'   => $post->post_title,
            'votes'   => $meta[0]['votes'],
            'answers' => $answers
        );

        $sofs[] = $sof;
    }

    usort($sofs, "intcmp");
    $sofs = array_reverse($sofs);

    if ( $count > 0 ) {
        $stats['poll']['sofs'] = array_slice($sofs, 0, $count);
    } else {
        $stats['poll']['sofs'] = $sofs;
    }
}

// tax parameter
if ( $_GET['tax'] ) {
    $tax_args = array(
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
    
    $categories = get_categories( $tax_args );

    if ( $categories['errors'] ) {
        die(utf8_decode($categories['errors']['invalid_taxonomy'][0]));
    }

    $taxonomies = array();

    foreach ( $categories as $cat ) {
        $args['posts_per_page'] = -1 ;
        $args['tax_query'] = array(
            array(
                'taxonomy' => $_GET['tax'],
                'field' => 'term_id',
                'terms' => $cat->term_id
            )
        );

        $posts = get_posts($args);
        $post_status = wp_list_pluck( $posts, 'post_status');
        $tax_stats = array_count_values($post_status);
        $tax_total = array_sum($tax_stats);

        arsort($tax_stats);

        $taxonomy = array(
            'name'   => $cat->name,
            'total'  => $tax_total,
            'status' => $tax_stats
        );

        if ( $tax_total > 0  ) {
            $taxonomies[] = $taxonomy;
        }
    }

    usort($taxonomies, "intcmp");
    $taxonomies = array_reverse($taxonomies);

    if ( $count > 0 ) {
        $stats['taxonomy'] = array_slice($taxonomies, 0, $count);
    } else {
        $stats['taxonomy'] = $taxonomies;
    }
}

// output format
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
        <?php print_taxonomy_stats($stats); ?>
    <?php elseif ( $stats['poll'] ) : ?>
        <?php print_poll_stats($stats); ?>
    <?php endif; ?>
</stats>

<?php
    rss_enclosure();
    do_action('rss2_item');
    wp_reset_postdata();
?>

<?php } ?>
