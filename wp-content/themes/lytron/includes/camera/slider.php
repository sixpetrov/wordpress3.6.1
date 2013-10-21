<?php $slides = aki::get_option('slider_images'); ?>
<?php if (! is_array($slides)) return; ?>

<div class="container-slider">
    <div class="camera_wrap">
        <?php foreach ($slides as $slide): ?>
            <div data-src="<?php echo $slide['img']['url'] ?>" data-link="<?php echo trim($slide['slide_link']); ?>">
            </div>
        <?php endforeach; ?>
    </div>
</div>
