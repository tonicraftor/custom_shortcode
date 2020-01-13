<?php
function franchisegrids_function( $atts, $content = null ) {
  $html = '<div class="franchises">';
  extract(shortcode_atts(array(
   'post_type' => 'landing_pages',
   'orderby' => 'date',//date,name,rand
   'order' => 'ASC',
   'posts_per_page' => 9,
   'show_liquid_cap' => false,
   'content_trim' => 30,
   'has_nav' => false
  ), $atts));
  $querypage = get_query_var('paged');
  if(!$querypage)$querypage = get_query_var('page');
  $posts_query = array( // Query Settings for WP_Query loop below
    'post_type' => $post_type,	
    'orderby' => $orderby,
    'order' => $order,
    'posts_per_page' => $posts_per_page,
    'paged' => $querypage
  );
  $thequery = new WP_Query($posts_query);
  while( $thequery->have_posts() ){
    $thequery->the_post();
    $liquid_capital = strip_tags(get_the_term_list( $post->ID, 'investment', '',', ',''));
    $source = get_field('source_id');
    $cookie = $_COOKIE["savedfranchises"];
    $incookie = strpos( $cookie, "%" . $source . "--" ) !== false;
    $logo = get_field('logo');
    $html .= '<div class="franchise"><div class="franchise--content"><div class="franchise--content-img">';
    if($logo){
      $html .= '<img class="logo img-responsive" src="'.$logo['url'].'">';
    }
    $html .= '</div><div class="franchise--content-txt">';
    $html .= '<div id="title_'.$source.'" class="franchise--title">'.get_the_title().'</div>';
    if($show_liquid_cap){
      $html .= '<div class="franchise--fee">Liquid Capital Required: <span>$'.($liquid_capital?number_format($liquid_capital):'').'</span></div>';
    }
    $html .= '<p>'.wp_trim_words(get_field('overview_content'), $content_trim, '...').'</p>';
    $html .= '<a href="javascript:addToMine( \''.$source.'\', this )" id="'.$source.'_href" class="btn btn-orange"';
    $html .= ($incookie?' style="display:none"':'').'>Add to info request</a>';
		$html .= '<a id="remove_'.$source.'" href="javascript:removeMe(\''.$source.'\')" class="btn btn-grey"';
    $html .= ($incookie?'':' style="display:none"').'>Remove</a>';
		$html .= '</div></div><a href="'.esc_url(get_permalink()).'" class="franchise--link"><p>Learn More</p></a></div>';
  }
	$html .= '</div>';
  //navigation
  if($has_nav){
    $html .= '<div class="posts-navigation">';
    $big = 999999999; // need an unlikely integer
    $html .= paginate_links(array(
      'base' => str_replace( $big, '%#%', get_pagenum_link( $big )),
      'format' => '?page=%#%',
      'current' => max( 1, $querypage ),
      'total' => $thequery->max_num_pages,
      'show_all' => true
    ));
    $html .= '</div>';
  }
  wp_reset_postdata();
	return $html;
}
add_shortcode('franchisegrids', 'franchisegrids_function');