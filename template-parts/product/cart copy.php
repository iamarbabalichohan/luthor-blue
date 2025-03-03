<?php
function get_cart_items() {
    // Ensure WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return [];
    }

    $cart = WC()->cart->get_cart();
    $cart_items = [];

    foreach ($cart as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];

        $cart_items[] = [
            'id' => $product->get_id(),
            'name' => $product->get_name(),
            'price' => $product->get_price(),
            'quantity' => $cart_item['quantity'],
            'subtotal' => wc_price($cart_item['line_subtotal']),
            'total' => wc_price($cart_item['line_total']),
            'image' => wp_get_attachment_image_src($product->get_image_id(), 'thumbnail')[0] ?? '',
            'permalink' => get_permalink($product->get_id()),
        ];
    }

    return $cart_items;
}

// Example usage
$cart_items = get_cart_items();
?>

<?php foreach ($cart_items as $item): ?>
    <div class="mt-4">
        <div class="flex border-b-2 border-dark items-center gap-4 sm:gap-8">
            <img class="w-16 h-16 sm:w-[134px] sm:h-[104px]" src="<?php echo esc_url($item['image']); ?>" alt="<?php echo esc_attr($item['name']); ?>">
            <div class="flex-1">
                <div class="flex justify-between items-center">
                    <h1 class="font-bold text-[14px] sm:text-[17px]"><?php echo esc_html($item['name']); ?></h1>
                    <span class="cursor-pointer text-xl sm:text-2xl">&times;</span>
                </div>
                <p class="text-[10px] sm:text-[12px]">Product description goes here</p>
                <div class="flex justify-between items-center mt-4 sm:mt-8">
                    <h1 class="text-[14px] sm:text-[12px]">$<?php echo esc_html(number_format($item['price'], 2)); ?></h1>
                    <div class="flex gap-2 sm:gap-4 p-1">
                        <span class="cursor-pointer">-</span>
                        <span><?php echo esc_html($item['quantity']); ?></span>
                        <span class="cursor-pointer">+</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>