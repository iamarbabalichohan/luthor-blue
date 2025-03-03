<?php
/// functions.php
// Enqueue Styles
function luther_blue_enqueue_styles()
{
    wp_enqueue_style('luther-blue-style', get_template_directory_uri() . '/assets/css/tailwind.css', array(), filemtime(get_template_directory() . '/assets/css/tailwind.css'));
    wp_enqueue_style('luther-blue-custom-style', get_template_directory_uri() . '/assets/css/custom-css.css', array(), filemtime(get_template_directory() . '/assets/css/custom-css.css'));
}
add_action('wp_enqueue_scripts', 'luther_blue_enqueue_styles');

// Filter Primary Menu Items
function filter_primary_menu_items($items, $args)
{
    if ($args->theme_location !== 'primary') {
        return $items;
    }
    $exclude_pages = ['cart', 'checkout', 'my-account', 'shop'];
    return array_filter($items, function ($item) use ($exclude_pages) {
        return !in_array(strtolower($item->post_name), $exclude_pages);
    });
}
add_filter('wp_get_nav_menu_items', 'filter_primary_menu_items', 10, 2);

// Price Variation Script
add_action('wp_enqueue_scripts', 'custom_variation_price_update');
function custom_variation_price_update()
{
    if (is_product()) {
        wp_enqueue_script('custom-variation-price1', get_template_directory_uri() . '/assets/js/variation-price.js', array('jquery'), null, true);

        // ✅ Use the correct handle here (same as enqueue)
        wp_localize_script('custom-variation-price1', 'wc_ajax_url', array(
            'ajax_url' => admin_url('admin-ajax.php') // Correct WooCommerce AJAX URL
        ));
        // ✅ Ensure localization uses the correct handle
        wp_localize_script('custom-variation-price1', 'wc_ajax_obj', array(
            'ajax_url' => admin_url('admin-ajax.php') // Correct WooCommerce AJAX URL
        ));
    }
}


// ✅ Get Cart Items via AJAX
add_action('wp_ajax_get_cart_items', 'ajax_get_cart_items');
add_action('wp_ajax_nopriv_get_cart_items', 'ajax_get_cart_items');

function ajax_get_cart_items()
{
    if (!class_exists('WooCommerce')) {
        wp_send_json_error(['message' => 'WooCommerce is not active']);
        return;
    }

    $cart = WC()->cart->get_cart();
    $cart_items = [];

    foreach ($cart as $cart_item_key => $cart_item) {
        $product = $cart_item['data'];
        $product_id = $product->get_id(); // ✅ Get product ID

        $cart_items[] = [
            "cart_item_key" => $cart_item_key,
            'id' => $product_id,
            'name' => $product->get_name(),
            'price' => $product->get_price(),
            'quantity' => $cart_item['quantity'],
            'subtotal' => wc_price($cart_item['line_subtotal']),
            'total' => wc_price($cart_item['line_total']),
            'image' => wp_get_attachment_image_src($product->get_image_id(), 'thumbnail')[0] ?? '',
            'permalink' => get_permalink($product_id),
            'sub_title' => get_field('sub_title', $product->get_id()),
            "count" => WC()->cart->get_cart_contents_count()

        ];
    }

    wp_send_json_success(['cart_items' => $cart_items]);
}


// ✅ Add to Cart via AJAX
add_action('wp_ajax_custom_add_to_cart', 'custom_add_to_cart_function');
add_action('wp_ajax_nopriv_custom_add_to_cart', 'custom_add_to_cart_function');

function custom_add_to_cart_function()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wc_ajax_nonce')) {
        wp_send_json_error(['message' => 'Security check failed.']);
        wp_die();
    }

    if (isset($_POST['product_id']) && isset($_POST['variation_id'])) {
        $product_id = absint($_POST['product_id']);
        $variation_id = absint($_POST['variation_id']);
        $quantity = isset($_POST['quantity']) ? absint($_POST['quantity']) : 1;

        if (!class_exists('WC_Cart')) {
            wp_send_json_error(['message' => 'WooCommerce not initialized.']);
            wp_die();
        }

        $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id);

        if ($cart_item_key) {
            wp_send_json_success([
                'message' => 'Product added to cart',
                'count' => WC()->cart->get_cart_contents_count()
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to add product']);
        }
    } else {
        wp_send_json_error(['message' => 'Invalid request parameters.']);
    }
    wp_die();
}


// ✅ Update Cart Count via AJAX
add_action('wp_ajax_update_cart_count', 'update_cart_count');
add_action('wp_ajax_nopriv_update_cart_count', 'update_cart_count');

function update_cart_count()
{
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wc_ajax_nonce')) {
        wp_send_json_error(['message' => 'Security check failed.']);
        wp_die();
    }

    wp_send_json_success([
        'count' => WC()->cart->get_cart_contents_count()
    ]);
}

// ✅ Enqueue Scripts and Pass AJAX Data
function enqueue_wc_ajax()
{
    wp_enqueue_script('wc-cart-ajax', get_template_directory_uri() . '/assets/js/cart-ajax.js', array('jquery'), null, true);

    wp_localize_script('wc-cart-ajax', 'wc_cart_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wc_ajax_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_wc_ajax');

// ✅ Allow Additional File Uploads
function allow_all_file_uploads($mime_types)
{
    $mime_types['otf'] = 'font/otf';
    return $mime_types;
}
add_filter('upload_mimes', 'allow_all_file_uploads');



// Enqueue necessary scripts
function enqueue_cart_update_scripts()
{
    wp_enqueue_script('cart-update-ajax', get_template_directory_uri() . '/assets/js/cart-update.js', array('jquery'), null, true);

    wp_localize_script('cart-update-ajax', 'cart_ajax_params', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('wc_ajax_nonce')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_cart_update_scripts');

// Fetch Cart Items via AJAX
// add_action('wp_ajax_get_cart_items', 'get_cart_items_ajax');
// add_action('wp_ajax_nopriv_get_cart_items', 'get_cart_items_ajax');

// function get_cart_items_ajax()
// {
//     if (!class_exists('WooCommerce')) {
//         wp_send_json_error(['message' => 'WooCommerce is not active']);
//         return;
//     }

//     $cart_items = [];
//     foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
//         $product = $cart_item['data'];
//         ////
//         $cart_items[] = [
//             "cart_item_key" => $cart_item_key,
//             'id' => $product->get_id(),
//             'name' => $product->get_name(),
//             'price' => wc_price($product->get_price()),
//             'quantity' => $cart_item['quantity'],
//             'subtotal' => wc_price($cart_item['line_subtotal']),
//             'image' => wp_get_attachment_image_src($product->get_image_id(), 'thumbnail')[0] ?? '',
//             'sub_title' => get_field('sub_title', $product->get_id()),
//             "count" => WC()->cart->get_cart_contents_count()
//         ];
//     }

//     wp_send_json_success(['cart_items' => $cart_items, 'cart_count' => WC()->cart->get_cart_contents_count(), 'cart_total' => WC()->cart->get_total()]);
// }


// ✅ Update Item Quantity via AJAX
// add_action('wp_ajax_update_cart_quantity', 'update_cart_quantity');
// add_action('wp_ajax_nopriv_update_cart_quantity', 'update_cart_quantity');

// function update_cart_quantity() {
//     if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wc_ajax_nonce')) {
//         wp_send_json_error(['message' => 'Security check failed.']);
//         wp_die();
//     }

//     $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
//     $quantity_change = isset($_POST['quantity_change']) ? intval($_POST['quantity_change']) : 0;

//     foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
//         if ($cart_item['product_id'] == $product_id) {
//             $new_quantity = max(1, $cart_item['quantity'] + $quantity_change);
//             WC()->cart->set_quantity($cart_item_key, $new_quantity);
//             break;
//         }
//     }

//     wp_send_json_success();
// }



function enqueue_cart_script()
{
    wp_enqueue_script('cart-script', get_template_directory_uri() . '/assets/js/cart-script.js', array('jquery'), null, true);

    // Pass AJAX URL to JavaScript
    wp_localize_script('cart-script', 'ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));
}
add_action('wp_enqueue_scripts', 'enqueue_cart_script');



add_action('wp_ajax_get_cart_total', 'get_cart_total');
add_action('wp_ajax_nopriv_get_cart_total', 'get_cart_total');

function get_cart_total()
{
    if (!WC()->cart) {
        wp_send_json_error(['message' => 'Cart not found.']);
        wp_die();
    }

    $cart_total = WC()->cart->get_total(); // Gets formatted total including currency symbol

    wp_send_json_success(['total' => $cart_total]);
    wp_die();
}




add_filter('woocommerce_cart_item_remove_link', 'custom_remove_cart_link', 10, 2);
function custom_remove_cart_link($link, $cart_item_key)
{
    return '<button class="remove-cart-item" data-key="' . esc_attr($cart_item_key) . '">❌</button>';
}
add_action('wp_ajax_remove_cart_item', 'remove_cart_item');
add_action('wp_ajax_nopriv_remove_cart_item', 'remove_cart_item');

function remove_cart_item()
{
    if (!isset($_POST['cart_item_key'])) {
        wp_send_json_error(['message' => 'Invalid request']);
    }

    $cart_item_key = sanitize_text_field($_POST['cart_item_key']); // Use cart key directly

    if (WC()->cart->remove_cart_item($cart_item_key)) {
        wp_send_json_success(['message' => 'Item removed', "cart_total_price" => WC()->cart->get_total(), 'cart_count' => WC()->cart->get_cart_contents_count()]);
    } else {
        wp_send_json_error(['message' => 'Failed to remove item']);
    }
}
function enqueue_remove_cart_script()
{
    wp_enqueue_script('remove-cart-item', get_template_directory_uri() . '/assets/js/remove-cart.js', ['jquery'], null, true);
    wp_localize_script('remove-cart-item', 'cartAjax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('remove_cart_item_nonce')
    ]);
}
add_action('wp_enqueue_scripts', 'enqueue_remove_cart_script');





// ✅ Handle Cart Item Quantity Update (Increment & Decrement)
add_action('wp_ajax_update_cart_quantity', 'update_cart_quantity');
add_action('wp_ajax_nopriv_update_cart_quantity', 'update_cart_quantity');

function update_cart_quantity()
{
    // Security check
    // if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wc_cart_nonce')) {
    //     wp_send_json_error(['message' => 'Security check failed.']);
    //     wp_die();
    // }

    if (!isset($_POST['cart_item_key'], $_POST['quantity_change'])) {
        wp_send_json_error(['message' => 'Invalid request parameters.']);
        wp_die();
    }

    $cart_item_key = sanitize_text_field($_POST['cart_item_key']);
    $quantity_change = intval($_POST['quantity_change']);

    // Fetch cart
    $cart = WC()->cart->get_cart();

    // Check if the item exists in the cart
    if (!isset($cart[$cart_item_key])) {
        wp_send_json_error(['message' => 'Item not found in cart.']);
        wp_die();
    }

    $cart_item = $cart[$cart_item_key];
    $product = $cart_item['data'];
    $current_quantity = $cart_item['quantity'];

    // Calculate new quantity
    $new_quantity = $current_quantity + $quantity_change;

    // Prevent quantity from going below 1
    if ($new_quantity < 1) {
        $new_quantity = 1;
    }

    // Check product stock availability
    $stock_quantity = $product->get_stock_quantity(); // Get stock quantity
    if ($stock_quantity !== null && $new_quantity > $stock_quantity) {
        wp_send_json_error(['message' => 'Not enough stock available.', 'stock_limit' => $stock_quantity]);
        wp_die();
    }

    // Update cart quantity
    WC()->cart->set_quantity($cart_item_key, $new_quantity);

    // Return updated cart details
    wp_send_json_success([
        'cart_count' => WC()->cart->get_cart_contents_count(),
        'cart_total' => WC()->cart->get_total(),
        'new_quantity' => $new_quantity,
        'message' => 'Cart updated successfully.'
    ]);
}


add_action('wp_ajax_generate_wc_cart_nonce', 'generate_wc_cart_nonce');
add_action('wp_ajax_nopriv_generate_wc_cart_nonce', 'generate_wc_cart_nonce');

function generate_wc_cart_nonce()
{
    wp_send_json_success(['nonce' => wp_create_nonce('wc_cart_nonce')]);
}


// function enqueue_custom_scripts() {
//     wp_enqueue_script('custom-variation-script', get_template_directory_uri() . '/js/variation-price.js', array('jquery'), null, true);

//     // Localize the script to pass wc_ajax_url
//     wp_localize_script('custom-variation-script', 'wc_ajax_obj', array(
//         'ajax_url' => admin_url('admin-ajax.php') // Correct WooCommerce AJAX URL
//     ));
// }
// add_action('wp_enqueue_scripts', 'enqueue_custom_scripts');
