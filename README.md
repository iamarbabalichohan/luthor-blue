# Luther Blue 4 - WordPress Theme

## Theme Details

- **Theme Name:** Luther Blue 4  
- **Theme URI:** [https://example.com/luther-blue](https://example.com/luther-blue)  
- **Author:** Arbab Ali Chohan  
- **Author URI:** [https://arbabali.com](https://arbabali.com)  
- **Description:** A basic WordPress theme named Luther Blue.  

## Overview
This WordPress theme is designed for eCommerce and built with **Tailwind CSS** for styling. Currently, only the **single product page** has been implemented.

## Folder Structure

```
LUTHER-BLUE/
│-- assets/
│   ├── css/       # Stylesheets
│   ├── fonts/     # Font files
│   ├── images/    # Theme images
│   ├── js/        # JavaScript files
│       ├── cart-ajax.js      # Handles AJAX calls for the cart
│       ├── cart-script.js    # General cart script
│       ├── cart-update.js    # Updates cart items
│       ├── remove-cart.js    # Removes items from cart
│       ├── variations.js     # Manages product variations
│-- template-parts/
│   ├── product/   # Product-related templates
│       ├── cart.php
│       ├── description.php
│       ├── gallery.php
│       ├── product-info.php
│       ├── related-products.php
│       ├── variations.php
│-- functions.php  # Theme functions
│-- header.php     # Header template
│-- footer.php     # Footer template
│-- index.php      # Main index file
│-- single-product.php  # Single product page template
│-- style.css      # Theme styles
│-- tailwind.config.js  # Tailwind CSS configuration
│-- package.json   # Dependencies
│-- package-lock.json  # Dependency lock file
│-- postcss.config.js   # PostCSS configuration
│-- README.md     # Documentation
│-- screenshot.png  # Theme preview image
```

## Screenshot
Below is the preview of the theme:

![Luther Blue Theme](screenshot.png)

## Features
- **Tailwind CSS** for efficient styling
- **Ecommerce ready** (Single product page built)
- **Custom JavaScript** for cart functionality
- **Modular Template Structure**

## Installation
1. Download the theme files.
2. Upload the theme to your WordPress `wp-content/themes/` directory.
3. Activate the theme via the WordPress dashboard.
4. Ensure dependencies (if needed) are installed using `npm install`.

## License
This theme is licensed under the GPL-2.0+ license. See [License URI](http://www.gnu.org/licenses/gpl-2.0.html) for details.

## Author
[Arbab Ali Chohan](https://arbabali.com)
