<?php
$product = $args['product'] ?? null;

if (!$product)
    return;

$variations = $product->get_available_variations();
$variation_data = [];

foreach ($variations as $variation) {
    $variation_data[$variation['attributes']['attribute_size']] = [
        'id' => $variation['variation_id'],
        'price' => $variation['display_price']
    ];
}
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        var productVariations = <?php echo json_encode($variation_data); ?>;
    });
</script>

<h1 class="py-4 font-bold">Select Size</h1>
<div class="size-options">
    <?php foreach ($variation_data as $size => $data): ?>
        <button type="button" class="size-option rounded-[4px] w-16 h-8 bg-white mr-4 text-dark"
            data-size="<?php echo esc_attr($size); ?>" data-id="<?php echo esc_attr($data['id']); ?>"
            data-price="<?php echo esc_attr($data['price']); ?>">
            <?php echo esc_html($size); ?>
        </button>
    <?php endforeach; ?>
</div>