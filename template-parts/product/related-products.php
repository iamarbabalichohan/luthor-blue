<?php
// Get the global product object
global $product;

if (!$product) {
    return;
}
// Get the current product's categories
$product_categories = wp_get_post_terms($product->get_id(), 'product_cat', array('fields' => 'ids'));

if (!empty($product_categories)) {
    // Query for similar products in the same categories
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => 10, // Number of similar products to display
        'post__not_in' => array($product->get_id()), // Exclude current product
        'orderby' => 'rand', // Randomize products
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'id',
                'terms' => $product_categories,
                'operator' => 'IN'
            )
        )
    );

    $similar_products = new WP_Query($args);
    function relevant_product_loop($product_id, $name, $price, $image_url, $product_url, $has_button = true, $variation_id = null)
    {
        // Fetch the ACF field 'sub_title'
        $sub_title = get_field('sub_title', $product_id);
        ?>
        <div class="flex justify-start flex-col similar-product">
            <a href="<?php echo esc_url($product_url); ?>">
                <img class="w-[340px] md:w-[520px] md:min-w-[520px]" src="<?php echo esc_url($image_url); ?>"
                    alt="<?php echo esc_attr($name); ?>">
            </a>
            <div class="w-[268px] md:w-[408px] border-[#52494A] border-t-[1px]"></div>

            <!-- Display Product Name -->
            <h3 class="md:text-[18px] text-[16px] leading-[16px] font-[Söhne] mt-3 md:mt-4">
                <?php echo esc_html($name); ?>
            </h3>

            <!-- Display ACF Subtitle if available -->
            <?php if (!empty($sub_title)): ?>
                <p class="text-[14px] font-light text-gray-600"><?php echo esc_html($sub_title); ?></p>
            <?php endif; ?>

            <p class="text-[13px] font-medium">
                <?php echo esc_html(get_woocommerce_currency_symbol() . number_format($price, 2)); ?>
            </p>

            <?php if ($has_button): ?>
                <button
                    class="add-to-cart similar-product-cart-btn bg-dark w-[268px] md:w-[408px] h-[56px] flex items-center justify-center text-white text-[14px] md:text-[14px] font-[400] mt-4 md:mt-5 mb-6 md:mb-10"
                    data-id="<?php echo esc_attr($product_id); ?>" data-variation-id="<?php echo esc_attr($variation_id); ?>">
                    <span class="sohne-400">Add to your cart -
                        <?php echo esc_html(get_woocommerce_currency_symbol() . number_format($price, 2)); ?>
                    </span>
                </button>
            <?php endif; ?>
        </div>
        <?php
    }
    $section_height = "";
    if ($similar_products->have_posts()) {
        // $section_height = "min-h-[570px] md:min-h-[899px]";
    }

    ?>


    <section
        class="bg-none md:bg-[#E9E6DA] p-[30px] sm:p-[60px] text-[32px] leading-[32px] md:text-[32px] text-dark <?php echo $section_height; ?>">
        <h2>You may also like</h2>
        <div class="custom-scrollbar overflow-x-auto pb-6 md:pb-10 mt-10 md:mt-16">
            <div class="flex space-x-[40px] md:space-x-[100px] items-start">
                <?php

                if ($similar_products->have_posts()) {
                    while ($similar_products->have_posts()):
                        $similar_products->the_post();
                        $product_id = get_the_ID();
                        $product_obj = wc_get_product($product_id);
                        $product_name = get_the_title();
                        $product_url = get_permalink();
                        $product_price = wc_get_price_to_display($product_obj);
                        $product_image = get_the_post_thumbnail_url($product_id, 'medium') ?: get_template_directory_uri() . '/assets/images/placeholder.png';

                        // Get first variation ID if available
                        $variation_id = null;
                        if ($product_obj->is_type('variable')) {
                            $variations = $product_obj->get_available_variations();
                            if (!empty($variations)) {
                                $variation_id = $variations[0]['variation_id'];
                                $product_price = $variations[0]['display_price']; // Use first variation's price
                            }
                        }

                        // Call the function to display the product
                        relevant_product_loop($product_id, $product_name, $product_price, $product_image, $product_url, true, $variation_id);
                    endwhile;
                    wp_reset_postdata();
                } else {
                    echo "<p>No Relevant Posts Found..</p>";
                }
}
?>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {


    }

    );
</script>