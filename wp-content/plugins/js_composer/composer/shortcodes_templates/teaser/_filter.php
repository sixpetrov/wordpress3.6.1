<?php
/**
 * Create filter
 * {{
 */
if( $filter === 'yes' && !empty($this->filter_categories)):
    $categories_array = $this->getFilterCategories();
?>
    <ul class="categories_filter clearfix">
        <li class="active"><a href="#" data-filter="*"><?php _e('All', 'js_composer') ?></a></li>
    <?php foreach($this->getFilterCategories() as $cat): ?>
        <li><a href="#" data-filter=".grid-cat-<?php echo $cat->term_id ?>"><?php echo esc_attr($cat->name) ?></a></li>
    <?php endforeach; ?>
    </ul><div class="clearfix"></div>
<?php endif; ?>
