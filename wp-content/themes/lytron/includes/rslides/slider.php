<?php $slides = aki::get_option('slider_images'); ?>
<?php if (! is_array($slides)) return; ?>

<div class="container-slider">
    <div id="showcase">
        <ul class="rslides">
        <?php foreach ($slides as $slide): ?>
            <li><a href="<?php echo trim($slide['slide_link']) ?>"><img src="<?php echo $slide['img']['url'] ?>" alt="<?php bloginfo('name') ?>"/></a></li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>