<?php if($post->thumbnail): ?>
<div class="post-thumb">
    <?php echo $this->getImageLink($post, $image_css) ?>
</div>
<?php endif; ?>