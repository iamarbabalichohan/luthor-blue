<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <header>
        <div class="container">
            <h1><?php bloginfo('name'); ?></h1>
            <nav>
                <?php
                $menu_args = array(
                    'theme_location' => 'primary',
                    'exclude' => implode(',', [
                        get_option('woocommerce_cart_page_id'),
                        get_option('woocommerce_checkout_page_id'),
                        get_option('woocommerce_myaccount_page_id'),
                        get_option('woocommerce_shop_page_id')
                    ])
                );

                wp_nav_menu($menu_args);

                ?>
            </nav>
        </div>
    </header>