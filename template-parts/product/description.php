<?php
$product = $args['product'] ?? null;

if (!$product) return;

function description_section($img, $title, $body) {
    if (!$img && !$title && !$body) return;
?>
<section class="border-t-[1px] border-b-[1px] border-dark">
    <div class="flex flex-col p-[20px] md:flex-row">
        <?php if ($img): ?>
        <div class="w-full md:w-1/2 h-[300px]" style="background-image: url('<?php echo esc_url($img); ?>'); background-size: cover;">
        </div>
        <?php endif; ?>
        <div class="w-full md:w-1/2 p-[60px]">
            <h2 class="mb-2"><?php echo esc_html($title); ?></h2>
            <p><?php echo esc_html($body); ?></p>
        </div>
    </div>
</section>
<?php
}

description_section(get_field('description_image_1', $product->get_id()), get_field('description_title_1', $product->get_id()), get_field('description_body_text_1', $product->get_id()));
?>
