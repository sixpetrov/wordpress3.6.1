<?php

class WPBakeryShortCode_VC_Posts_Grid extends WPBakeryShortCode {
    protected $filter_categories = array();
    protected $query = false;
    protected $loop_args = array();
    protected $taxonomies = false;
    protected $partial_paths = array();

    protected function getCategoriesCss($post_id) {
        $categories_css = '';
        $post_categories = wp_get_object_terms($post_id, $this->getTaxonomies());
        foreach($post_categories as $cat) {
            if(!in_array($cat->term_id, $this->filter_categories)) {
                $this->filter_categories[] = $cat->term_id;
            }
            $categories_css .= ' grid-cat-'.$cat->term_id;
        }
        return $categories_css;
    }
    protected function getTaxonomies() {
        if($this->taxonomies === false) {
            $this->taxonomies = get_object_taxonomies(!empty($this->loop_args['post_type']) ? $this->loop_args['post_type'] : get_post_types(array('public' => false, 'name' => 'attachment'), 'names', 'NOT'));
        }
        return $this->taxonomies;
    }
    protected function getLoop($loop) {
        list($this->loop_args, $this->query)  = vc_build_loop_query($loop);
    }
    protected function spanClass($grid_columns_count) {
        $teaser_width = '';
        switch ($grid_columns_count) {
            case '1' :
                $teaser_width = 'vc_span12';
                break;
            case '2' :
                $teaser_width = 'vc_span6';
                break;
            case '3' :
                $teaser_width = 'vc_span4';
                break;
            case '4' :
                $teaser_width = 'vc_span3';
                break;
            case '5':
                $teaser_width = 'vc_span10';
                break;
            case '6' :
                $teaser_width = 'vc_span2';
                break;
        }
        //return $teaser_width;
        $custom = get_custom_column_class($teaser_width);
        return $custom ? $custom : $teaser_width;
    }
    protected function getMainCssClass($filter) {
        return 'wpb_'.($filter==='yes' ? 'filtered_' : '').'grid';
    }
    protected function getFilterCategories() {
        return get_terms($this->getTaxonomies(), array(
        'orderby' => 'name',
        'include' => implode(',', $this->filter_categories)
        ));
    }
    protected function getPostThumbnail($grid_layout, $post_id, $grid_thumb_size) {
        if ( in_array($grid_layout, array('title_thumbnail_text', 'thumbnail_title_text', 'thumbnail_text', 'thumbnail_title', 'thumbnail', 'title_text') ) ) {
            return  wpb_getImageBySize(array( 'post_id' => $post_id, 'thumb_size' => $grid_thumb_size ));
        }
        return false;
    }
    protected function getPostContent($grid_content='teaser') {
        $content = $grid_content == 'teaser' ?
                apply_filters('the_excerpt', get_the_excerpt()) :
                str_replace(']]>', ']]&gt;', apply_filters('the_content', get_the_content()));
        return wpautop($content);
    }
    protected function getPostLink($grid_link, stdClass $post) {
        if($grid_link == 'link_post') {
            $url = get_permalink($post->id);
            $title = sprintf( esc_attr__( 'Permalink to %s', 'js_composer' ), the_title_attribute( 'echo=0' ) );
            return array('url_image' => $url,
                         'url_title' => $url,
                         'title_image' => $title,
                         'title_title' => $title);
        } elseif ($grid_link == 'link_image') {
            $video = get_post_meta($post->id, "_p_video", true);
            $url =  empty($video) && $post->thumbnail && isset($post->thumbnail_data['p_img_large'][0]) ? $post->thumbnail_data['p_img_large'][0] : $video;
            $title = the_title_attribute('echo=0');
            return array('url_image' => $url,
                'url_title' => $url,
                'title_image' => $title,
                'title_title' => $title);
        } elseif($grid_link == 'link_image_post') {
            $video = get_post_meta($post->id, "_p_video", true);
            $url =  empty($video) && $post->thumbnail && isset($post->thumbnail_data['p_img_large'][0]) ? $post->thumbnail_data['p_img_large'][0] : $video;
            return array('url_image' => $url,
                'url_title' => get_permalink($post->id),
                'title_image' => the_title_attribute('echo=0'),
                'title_title' => sprintf( esc_attr__( 'Permalink to %s', 'js_composer' ), the_title_attribute( 'echo=0' ) ));
        }
        return false;
    }
    protected function setLinkTarget($grid_link_target = '') {
        $this->link_target = $grid_link_target=='_blank' ? ' target="_blank"' : '';
    }
    protected function getImageLink($post, $css_class = '') {
        if($post->link!==false && !empty($post->link['url_image'])) {
            return '<a href="'.$post->link['url_image'].'" class="'.$css_class.'"'.$this->link_target.' title="'.$post->link['title_image'].'">'.$post->thumbnail.'</a>';
        }
        return $post->thumbnail;
    }
    protected function getTitleLink($post, $css_class = '') {
        if($post->link!==false && !empty($post->link['url_title'])) {
            return '<a href="'.$post->link['url_title'].'" class="'.$css_class.'"'.$this->link_target.' title="'.$post->link['title_title'].'">'.$post->title.'</a>';
        }
        return $post->title;
    }
    protected function getPartial($partial) {
        // Check template path in shortcode's mapping settings
        if(isset($this->partial_paths[$partial])) return $this->partial_paths[$partial];

        // Check template in theme directory
        $user_template = WPBakeryVisualComposer::getUserTemplate($this->getPartialFilename($partial));
        if(is_file($user_template)) {
            return $this->setPartialTemplate($partial, $user_template);
        }
        // Check default place
        $default_dir = WPBakeryVisualComposer::defaultTemplatesDIR();
        $default_dir.$this->getPartialFilename($partial);
        if(is_file($default_dir.$this->getPartialFilename($partial))) {
            return $this->setPartialTemplate($partial, $default_dir.$this->getPartialFilename($partial));
        }
    }
    protected function setPartialTemplate($partial, $path) {
        $this->partial_paths[$partial] = $path;
        return $path;
    }
    protected function getPartialFilename($key) {
        return 'teaser/'.$key.'.php';
    }
}