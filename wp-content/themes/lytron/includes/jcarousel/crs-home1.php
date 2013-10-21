<div id="crsWrapper1">
    <a id="crs1_left" class="crsArrow arrowleft sprites" href="javascript:void(0)">Prev</a>

    <div id="crsInner1" class="crsInner">
        <?php $items = aki::get_option('carousel_images'); ?>
        <?php $crs_speed = aki::get_option('carousel_speed'); ?>

        <ul>
        <?php if (is_array($items)): ?>
            <?php foreach ($items as $item): ?>
                <li>
                    <a href="<?php echo $item['url'] ?>" rel="prettyPhoto" title="<?php echo $item['description'] ?>">
                        <img width="109" height="109" src="<?php echo $item['sizes']['thumbnail'] ?>" alt="<?php echo $item['title'] ?>"/>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
        </ul>
    </div>

    <a id="crs1_right" class="crsArrow arrowright sprites" href="javascript:void(0)">Next</a>

    <script type="text/javascript">
        jQuery(document).ready(function($)
        {
            var crsAuto = parseInt('<?php echo trim($crs_speed) ?>');

            $('#crsInner1').jCarouselLite({
                auto: crsAuto,
                speed: 800,
                visible: 5,
                btnPrev: '#crs1_left',
                btnNext: '#crs1_right'
            });
        });
    </script>
</div>