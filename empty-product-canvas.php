<?php
/**
 * Template Name: Empty Product Canvas
 * Description: A minimal WooCommerce product page template.
 */

defined('ABSPATH') || exit;

get_template_part('template-parts/product/header'); // Custom Header

if (!function_exists('is_product') || !is_product()) {
    return;
}

global $product;

// Ensure WooCommerce global product object is properly defined
if (!is_a($product, 'WC_Product')) {
    $product = wc_get_product(get_the_ID());
}

// Fetch categories
$categories = get_terms(array(
    'taxonomy'   => 'product_cat',
    'hide_empty' => true,
));

get_template_part('template-parts/product/gallery', null, ['product' => $product]);
get_template_part('template-parts/product/product-info', null, ['product' => $product, 'categories' => $categories]);
get_template_part('template-parts/product/variations', null, ['product' => $product]);
get_template_part('template-parts/product/cart');
get_template_part('template-parts/product/description', null, ['product' => $product]);
get_template_part('template-parts/product/related-products');

get_footer();
?>
