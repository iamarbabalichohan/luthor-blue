<div id="cart-overlay" class="bg-black hidden bg-opacity-50 h-screen w-screen fixed top-0 left-0"></div>

<section id="cart"
    class="overflow-y-scroll fixed top-0 right-0 w-full sm:w-[550px] px-[30px] sm:px-[70px] pt-[30px] sm:pt-[70px] h-full bg-[#F7F4EC] transform translate-x-full transition-transform duration-1000 ease-in-out">
    <div
        class="flex text-[20px] sm:text-[31px] leading-[32px] border-b-2 border-dark justify-between items-center pb-[20px] sm:pb-[30px]">
        <h1>Your Cart (<span id="sidebar-cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>)</h1>
        <span id="close-cart" class="text-3xl sm:text-4xl cursor-pointer">&times;</span>
    </div>

    <div id="cart-items-container">

    </div>
    <!-- <button id="getCartButton">Get Cart Items</button> -->
    <div class="border-t-2 mt-[80px] sm:mt-[320px] border-dark pt-[20px] sm:pt-[30px]">
        <div class="flex justify-between">
            <h1 class="text-[12px] sm:text-[13px] sohne-400 uppercase">Total</h1>
            <span id="cart-total" class="text-[12px] sm:text-[13px] cartTotal sohne-400">
                $<?php echo WC()->cart->get_total(); ?> </span>
        </div>
        <button
            class="w-full bg-[#E9E6DA] py-2 mt-4 text-[14px] sm:text-[13px] text-dark sohne-400 flex items-center justify-center space-x-3 sohne-400">
            <span class="sohne-400">GO TO CHECKOUT</span>
            <img src="<?php echo get_template_directory_uri()."/assets/images/dot.png"; ?>" alt="">
            <span id="cart-total-text" class="cartTotal sohne-400">$<?php echo WC()->cart->get_total(); ?></span>
        </button>
        <p class="text-xs sm:text-[12px] mt-2 text-gray-600">Free Standard Shipping Worldwide With Orders Over $80</p>
    </div>
</section>