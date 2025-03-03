<?php
/**
 * Template Name: Empty Product Canvas
 * Description: A minimal WooCommerce product page template.
 */

defined('ABSPATH') || exit;

get_template_part('template-parts/header-custom');

if (!function_exists('is_product') || !is_product()) {
    return;
}

// Ensure WooCommerce global product object is properly defined
global $product;



$categories = get_terms(array(
    'taxonomy' => 'product_cat', // WooCommerce product category
    'hide_empty' => true, // Hide empty categories
));

// If $product is not an object, try to retrieve it again
if (!is_a($product, 'WC_Product')) {
    $product = wc_get_product(get_the_ID());
}


// Fetch variation data
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

<?php
$images = array();
if ($product) {
    $attachment_ids = $product->get_gallery_image_ids();

    if (!empty($attachment_ids)) {


        foreach ($attachment_ids as $attachment_id) {
            array_push($images, wp_get_attachment_image_url($attachment_id, 'full'));
            // echo '<img src="' . esc_url($image_url) . '" alt="Product Gallery Image" class="product-gallery-image">';
        }
    }
}
$obj = array();
$i = 0;
foreach ($images as $image) {
    // array_push($obj, {
    //     id: $i,
    //     src: $image
    // });
    $obj[$i] = array(
        'id' => $i,
        'src' => $image
    );
    $i++;
}
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {

    });
    const galleryImgs = <?php echo json_encode($obj); ?>;
</script>
<?php
global $post;




$product = wc_get_product($post->ID); // ✅ Fetch the WooCommerce product object

if ($product) {
    $product_id = $product->get_id();
    // Top Section Information
    $excerpt = get_field('excerpt', $product_id);
    $features = get_field('features', $product_id);
    $aroma = get_field('aroma', $product_id);
    $key_ingredients = get_field('key_ingredients', $product_id);


    /// Description Section Information
    $desc_title_1 = get_field('description_title_1', $product_id);
    $desc_body_1 = get_field('description_body_text_1', $product_id);
    $desc_img_1 = get_field('description_image_1', $product_id);
    $desc_title_2 = get_field('description_title_2', $product_id);
    $desc_body_2 = get_field('description_body_text_2', $product_id);
    $desc_img_2 = get_field('description_image_2', $product_id);
}

/**
 * Function to display description sections
 */
function description_section($img, $title, $body, $img_height, $img_width, $isReverse = false)
{
    if (!$img && !$title && !$body) {
        return; // Prevent rendering if no content is provided
    }
    ?>
    <section class="border-t-[1px] border-b-[1px] border-dark">
        <div
            class="flex flex-col p-[20px] md:p-0 md:flex-row justify-center <?php echo $isReverse ? "md:flex-row-reverse" : ""; ?>">
            <?php if ($img): ?>
                <div class="w-full p-[40px] md:w-1/2 h-[300px] md:h-auto"
                    style="min-height: <?php echo $img_height; ?>; background-image: url('<?php echo esc_url($img); ?>'); background-size: cover; background-position: center;">
                </div>
            <?php endif; ?>
            <div class="w-full md:w-1/2 py-[20px]  md:p-[60px] w-[<?php echo $img_width; ?>] flex items-center">
                <div>
                    <?php if ($title): ?>
                        <h2 class="font-[500] md:[600] mb-2 md:mb-4 text-[14px] md:leading-[38px] md:text-[20px] sohne-400">
                            <?php echo esc_html($title); ?>
                        </h2>
                    <?php endif; ?>
                    <?php if ($body): ?>
                        <p class="text-[#19281F] text-[20px] md:font-[400] md:leading-[44px] md:text-[32px]">
                            <?php echo esc_html($body); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <?php
}
?>


<section class="">
    <div class="flex text-white justify-center bg-dark w-full h-6 relative">
        <h1 class="text-[10px] md:text-[13px] leading-[15.25px] md:font-[400] p-1">Free Shipping Australia
            With Order Over $80</h1>
        <span
            class="text-[10px] absolute hidden md:flex items-center h-full right-5 py-2 md:text-[16px] cursor-pointer"><img
                src="<?php echo get_template_directory_uri() . "/assets/images/cross.png" ?>" alt="" srcset=""></span>
    </div>

    <nav class="flex md:flex justify-between md:gap-24 p-4 md:flex-row relative">
        <button id="menu-btn" class="md:hidden text-xl"><img
                src="<?php echo get_template_directory_uri() . "/assets/images/group.png" ?>" alt="" srcset=""></button>
        <div id="sidebar"
            class="fixed top-0 left-0 w-full h-full bg-orange-50 transform -translate-x-full md:translate-x-0 transition-transform duration-500 ease-in-out md:static md:w-auto md:bg-transparent p-4 md:p-0 md:ml-[70px]">
            <span id="close-sidebar"
                class="text-3xl absolute top-4 right-6 md:hidden sm:text-4xl cursor-pointer">&times;</span>
            <ul
                class="flex md:text-[14px] md:leading-[16px] md:font-[400] text-[Söhne] flex-col md:py-4 md:flex-row gap-6 cursor-pointer">
                <li>Wash</li>
                <li>Breath</li>
                <li>Elevate</li>
                <li>Feel</li>
                <li class="md:hidden">Shop All</li>
                <li class="md:hidden">Login</li>
            </ul>
        </div>
        <h1 class="text-[30px] md:text-[38px] md:font-[400] md:leading-[22px] ml-[50px] font-[500] md:my-[12px] ">LUTHER
            BLUE</h1>
        <h1 id="cart-btn" class="text-[12px] leading-[12px] font-[500] md:hidden my-[12px] mx-auto cursor-pointer">Cart
            (<span id="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>)</h1>
        <div class="hidden md:mr-[70px] md:flex gap-6 absolute top-10 right-0 w-full bg-white md:static md:w-auto md:bg-transparent p-4 md:p-0"
            id="sidebar2">
            <ul
                class="flex flex-col md:py-4 md:text-[14px] md:text-[Söhne] md:leading-[16px] md:flex-row gap-6 cursor-pointer">
                <li>Shop All</li>
                <li>Login</li>
                <li id="cart-btn-desktop" class="cursor-pointer">Cart (<span
                        id="cart-count-desktop"><?php echo WC()->cart->get_cart_contents_count(); ?></span>)</li>
            </ul>
        </div>
    </nav>

    <?php get_template_part("template-parts/product/cart") ?>

</section>

<section class="border-t-[1.5px] border-dark">
    <div class="flex flex-col md:flex-row min-h-[800px]">

        <div class="w-full md:w-[55%] flex flex-wrap-reverse border-r-[1px] border-dark">
            <div id="previewImages"
                class="w-full md:w-[140px] flex md:flex-col p-0 border-r-[1px] border-dark custom-scrollbar overflow-x-auto">
                <?php
                // $index = 0;
                // foreach ($images as $image) {
                //     if ($index !== 0) {
                //         echo '<img src="' . $image . '" alt="Thumbnail" class="w-1/3 md:w-[140px] product-gallery-img cursor-pointer" onclick="changeMainImage(this)">';
                //     }
                //     $index++;
                // }
                ?>
            </div>
            <div class="w-full p-[20px]  text-center md:hidden">
                <p class="sohne-400 flex text-[12px] leading-[16px] items-center justify-center space-x-2 mx-auto"><?php
                $category_count = count($categories); // Get total categories
                $current_index = 0; // Track loop index
                
                foreach ($categories as $category) {
                    $current_index++; // Increment counter
                    ?>
                        <span class="category sohne-400"><?php echo $category->name; ?></span>
                        <?php if ($current_index < $category_count) { // Only add dot if not last category ?>
                            <img src="<?php echo get_template_directory_uri() . "/assets/images/dot.png"; ?>" alt="dot"
                                class="category-separator">
                        <?php } ?>
                        <?php
                }
                ?>
                </p>
                <h1 class="text-[28px] py-4"><?php echo $product->get_name(); ?></h1>
                <p class="pb-4 text-[14px] leading-[20px]"><?php echo $excerpt; ?></p>
            </div>

            <div class="w-full md:flex-1 md:p-12 flex justify-center items-center">
                <img id="mainImage" class="product-gallery-img w-full md:w-[594px] h-auto" src="" alt="Main Image">
            </div>
        </div>

        <div
            class="w-full p-[20px] md:w-[45%] text-[14px] leading-[20px] text-[Söhne] md:text-[14px] md:leading-[20px] md:text-[Söhne] ">
            <div class="md:pt-18 md:p-16 w-full p-[12px] md:pr-20 sohne-300">
                <div class="hidden md:block">
                    <p class="flex text-[12px] leading-[16px] items-center space-x-2 ">
                        <?php
                        $category_count = count($categories); // Get total categories
                        $current_index = 0; // Track loop index
                        
                        foreach ($categories as $category) {
                            $current_index++; // Increment counter
                            ?>
                            <span class="category sohne-400"><?php echo $category->name; ?></span>
                            <?php if ($current_index < $category_count) { // Only add dot if not last category ?>
                                <img src="<?php echo get_template_directory_uri() . "/assets/images/dot.png"; ?>" alt="dot"
                                    class="category-separator">
                            <?php } ?>
                            <?php
                        }
                        ?>


                    </p>
                    <h1 class=" md:text-[32px] md:leading-[32px] md:font-[400] py-4"><?php echo $product->get_name(); ?>
                    </h1>
                    <p class=" border-b-[2px]  pb-4 border-black sohne-300"><?php echo $excerpt; ?></p>
                </div>
                <h1 class="font-bold pt-4 py-2 sohne-400 capitalize">Features</h1>
                <p class="border-b-[2px] pb-4 sohne-300"><?php echo $features; ?></p>
                <h1 class="font-bold pt-4 py-2 sohne-400 capitalize">Aroma</h1>
                <p class="border-b-[2px] pb-4 sohne-300"><?php echo $aroma; ?></p>
                <?php
                set_query_var('key_ingredients', $key_ingredients);
                get_template_part("template-parts/product/accordion");
                ?>

                <h1 class="py-4 font-bold sohne-400 capitalize">Select Size</h1>
                <div class="size-options">
                    <?php foreach ($variation_data as $size => $data): ?>
                        <button type="button" class="size-option  rounded-[4px] w-16 h-8 bg-white mr-4 text-dark sohne-300"
                            data-size="<?php echo esc_attr($size); ?>" data-id="<?php echo esc_attr($data['id']); ?>"
                            data-price="<?php echo esc_attr($data['price']); ?>">
                            <?php echo esc_html($size); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="custom-add-to-cart"
                    class="single_add_to_cart_button w-full text-[14px] leading-[16px] text-[Söhne] md:w-64 p-4 my-8 bg-dark text-white"
                    data-product-id="<?php echo esc_attr($product->get_id()); ?>" data-variation-id="">
                    Add To Your Cart - Select a Size
                </button>


                <!-- <button class="add-to-cart-main w-full md:w-64 p-4 my-8 bg-black text-white ">Add To Your Cart -
                    $22.99</button> -->
                <h1 class="font-bold text-center md:text-left sohne-400">Pay with Afterpay</h1>
                <p class="text-center md:text-left sohne-300">Select Afterpay at checkout to pay in four interest-free
                    instalments and enjoy your purchase imminently.</p>
            </div>
        </div>
    </div>
</section>


<!-- Display First Description Section -->
<?php description_section($desc_img_1, $desc_title_1, $desc_body_1, "766px", "594px"); ?>

<!-- Display Second Description Section -->
<?php description_section($desc_img_2, $desc_title_2, $desc_body_2, "472px", "514px", true); ?>

<?php get_template_part('template-parts/product/related-products'); ?>

<?php get_footer(); ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        document.getElementById("cart-total").textContent = parseFloat(document.getElementById("cart-total").textContent).toFixed(2) ///its a float it should have this format ${//anumber at max at two decimal places}
        document.getElementById("cart-total-text").textContent = parseFloat(document.getElementById("cart-total-text").textContent).toFixed(2)///its a float it should have this format ${//anumber at max at two decimal places}
    });
</script>