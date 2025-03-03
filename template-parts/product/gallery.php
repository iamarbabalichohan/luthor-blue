<?php
$product = $args['product'] ?? null;
$images = [];

if ($product) {
    $attachment_ids = $product->get_gallery_image_ids();
    foreach ($attachment_ids as $attachment_id) {
        $images[] = wp_get_attachment_image_url($attachment_id, 'full');
    }
}

?>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        const galleryImgs = <?php echo json_encode($images); ?>;
    });
</script>

<section class="border-t-[1.5px] border-dark">
    <div class="flex flex-col md:flex-row min-h-[800px]">
        <div class="w-full md:w-[55%] flex flex-wrap-reverse border-r-[1px] border-dark">
            <div id="previewImages" class="w-full md:w-[140px] flex md:flex-col border-r-[1px] border-dark">
                <?php foreach ($images as $image): ?>
                    <img src="<?php echo esc_url($image); ?>" alt="Thumbnail"
                        class="w-1/3 md:w-[140px] product-gallery-img cursor-pointer">
                <?php endforeach; ?>
            </div>
            <div class="w-full md:flex-1 flex justify-center items-center">
                <img id="mainImage" class="product-gallery-img w-full md:w-[594px]"
                    src="<?php echo esc_url($images[0] ?? ''); ?>" alt="Main Image">
            </div>
        </div>
    </div>
</section>