// variation-price.js

jQuery(document).ready(() => {
  document.querySelectorAll(".update-cart-btn").forEach((item) => {
    item.addEventListener("click", quantity_changer);
  });

  document.querySelectorAll(".remove-item").forEach((item) => {
    item.addEventListener("click", foo);
  });

  const menuBtn = document.getElementById("menu-btn");
  const sidebar = document.getElementById("sidebar");
  const closeSidebar = document.getElementById("close-sidebar");

  menuBtn.addEventListener("click", function () {
    sidebar.classList.remove("-translate-x-full");
  });

  closeSidebar.addEventListener("click", function () {
    sidebar.classList.add("-translate-x-full");
  });

  const cart = document.getElementById("cart");
  const cartBtn = document.getElementById("cart-btn");
  const cartBtnDesktop = document.getElementById("cart-btn-desktop");
  const closeCartBtn = document.getElementById("close-cart");

  function openCart() {
    document.getElementById("cart-overlay").classList.remove("hidden");
    getCartTotal();
    cart.classList.remove("translate-x-full");
    cart.classList.remove("hidden");
  }

  function closeCart() {
    document.getElementById("cart-overlay").classList.add("hidden");
    cart.classList.add("translate-x-full");
    setTimeout(() => {
      cart.classList.add("hidden");
    }, 1000);
  }

  function renderCartItems(cartItems) {
    const cartTotalDivs = document.querySelectorAll(".cartTotal");
    let sum = 0;
    cartItems.forEach((item) => {
      sum += item.price * item.quantity;
    });
    cartTotalDivs.forEach((div) => {
      div.innerHTML = "$" + parseFloat(sum).toFixed(2);
    });
    const cartContainer = document.getElementById("cart-items-container"); // Ensure this div exists in your HTML
    cartContainer.innerHTML = cartItems.map((item) => CartItem(item)).join("");
  }
  function getCartItems() {
    fetch(ajax_object.ajax_url + "?action=get_cart_items")
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          renderCartItems(data.data.cart_items);
          refreshNonce();
        } else {
          console.error("Error fetching cart items:", data);
        }
      })
      .catch((error) => console.error("AJAX Error:", error));
  }
  function CartItem(item) {
    return `
    <div class="flex items-center border-b-2 border-dark gap-4 sm:gap-8 p-4 sohne-300"
         data-product-id="${item.id}">
        <img class="w-16 h-16 sm:w-[134px] sm:h-[104px]" 
             src="${item.image}" 
             alt="${item.name}">
        <div class="flex-1">
            <div class="flex justify-between items-center">
                <h1 class="font-bold text-[14px] sm:text-[17px] sohne-400">${item.name}</h1>
                <span onclick="foo(this)" class="cursor-pointer text-xl sm:text-2xl" 
                      data-cart-item-key="${item.cart_item_key}">&times;</span>
            </div>
            <div class="flex justify-between items-center mt-4 sm:mt-8">
                <h1 class="text-[14px] sm:text-[12px] sohne-400">$${item.price}</h1>
                <div class="quantity-controls flex items-center gap-2 sm:gap-4 p-1">
                    <input type="hidden" id="wc_cart_nonce" value="${cartAjax.nonce}">
                    <span onclick="quantity_changer(this)"  class="cursor-pointer" data-quantity-change="-1" data-product-id="${item.id}" data-cart-item-key="${item.cart_item_key}" ><img src="/wp-content/themes/luther-blue/assets/images/minus.png" alt="-" class=""></span>
                    <span id="cart-item-${item.cart_item_key}-quantity" class="quantity sohne-300">${item.quantity}</span>
                    <span onclick="quantity_changer(this)" class="cursor-pointer" data-quantity-change="1" data-product-id="${item.id}" data-cart-item-key="${item.cart_item_key}" ><img src="/wp-content/themes/luther-blue/assets/images/plus-2.png" alt="+" class=""></span>
                </div>
            </div>
        </div>
    </div>
    `;
  }

  cartBtn?.addEventListener("click", openCart);
  cartBtnDesktop?.addEventListener("click", openCart);
  closeCartBtn?.addEventListener("click", closeCart);
  function getCartTotal() {
    fetch(ajax_object.ajax_url + "?action=get_cart_total")
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
        } else {
          console.error("Error fetching cart total:", data);
        }
      })
      .catch((error) => console.error("AJAX Error:", error));

    document.querySelectorAll(".remove-cart-item").forEach((button) => {
      button.addEventListener("click", function () {
        let cartItemKey = this.getAttribute("data-key");
        let formData = new FormData();
        formData.append("action", "remove_cart_item");
        formData.append("cart_item_key", cartItemKey);
        formData.append("nonce", cartAjax.nonce);

        this.textContent = "⏳";

        fetch(cartAjax.ajax_url, {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              let row = this.closest("tr");
              if (row) {
                row.style.transition = "opacity 0.3s";
                row.style.opacity = "0";
                setTimeout(() => row.remove(), 300);
              }
              document.querySelector(".cart-count").textContent =
                data.data.cart_count;
            } else {
              console.error("Error in response:", data);
            }
          })
          .catch((error) => console.error("Fetch Error:", error));
      });
    });

    document.querySelectorAll(".cart-quantity-btn").forEach(function (button) {
      button.addEventListener("click", function () {
        let cartItemKey = button.getAttribute("data-cart-item-key");
        let quantityChange = parseInt(button.getAttribute("data-change"));

        // Disable button to prevent multiple clicks
        button.disabled = true;

        let formData = new FormData();
        formData.append("action", "update_cart_quantity");
        formData.append("nonce", cart_ajax_params.nonce);
        formData.append("cart_item_key", cartItemKey);
        formData.append("quantity_change", quantityChange);

        fetch(cart_ajax_params.ajax_url, {
          method: "POST",
          body: formData,
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              let newQuantity = data.data.new_quantity;
              let cartTotal = data.data.cart_total;
              let cartCount = data.data.cart_count;

              // Update quantity on UI
              let cartItem = button.closest(".cart-item");
              if (cartItem) {
                let quantityElement = cartItem.querySelector(".cart-quantity");
                if (quantityElement) {
                  quantityElement.textContent = newQuantity;
                }
              }

              // Update cart total
              let cartTotalElement = document.querySelector(".cart-total");
              if (cartTotalElement) {
                cartTotalElement.innerHTML =
                  "$" + parseFloat(cartTotal).toFixed(2);
              }

              // Update cart count
              let cartCountElement = document.querySelector(".cart-count");
              if (cartCountElement) {
                cartCountElement.textContent = cartCount;
              }
            } else {
              alert(data.data.message); // Handle error messages
            }
          })
          .catch((error) => console.error("Error:", error))
          .finally(() => {
            // Re-enable button after request is complete
            button.disabled = false;
          });
      });
    });
  }

  function quantity_changer(event) {
    let cartItemKey = event.dataset.cartItemKey; // Correct dataset usage
    let quantityChange = parseInt(event.dataset.quantityChange);
    const quantity = document.getElementById(
      `cart-item-${cartItemKey}-quantity`
    ).innerHTML;
    if (quantity == 1 && quantityChange == -1) {
      foo(event);
      return;
    }
    // Security nonce (Ensure this is printed in your template)
    let nonce = document.getElementById("wc_cart_nonce").value;

    let formData = new FormData();
    formData.append("action", "update_cart_quantity");
    formData.append("cart_item_key", cartItemKey);
    formData.append("quantity_change", quantityChange);
    formData.append("nonce", nonce);

    fetch(wc_ajax_obj.ajax_url, {
      // ✅ Corrected AJAX URL reference
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          document.getElementById(
            `cart-item-${cartItemKey}-quantity`
          ).innerText = data.data.new_quantity;
          document.getElementById("cart-total").innerText = stringToElement(
            data.data.cart_total
          ).textContent;
          document.getElementById("cart-total-text").innerText =
            stringToElement(data.data.cart_total).textContent;
          document.getElementById("cart-count").innerText =
            data.data.cart_count;
          document.getElementById("sidebar-cart-count").innerText =
            data.data.cart_count;
          document.getElementById("cart-count-desktop").innerText =
            data.data.cart_count;
        } else {
          alert(data.data.message);
        }

        // ✅ Update nonce dynamically from response (to prevent expiry issues)
        if (data.data.new_nonce) {
          document.getElementById("wc_cart_nonce").value = data.data.new_nonce;
        }
      })
      .catch((error) => console.error("Error:", error));
  }
  function refreshNonce() {
    fetch(wc_ajax_obj.ajax_url + "?action=generate_wc_cart_nonce") // ✅ Corrected URL reference
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          document.getElementById("wc_cart_nonce").value = data.data.nonce;
        }
      })
      .catch((error) => console.error("Error refreshing nonce:", error));
  }

  getCartItems();
  // Set the first image as the selected one
  let selectedGalleryImage = galleryImgs[0].id;
  const mainImage = document.getElementById("mainImage");
  mainImage.src = galleryImgs[0].src;
  const previewImages = document.getElementById("previewImages");
  // Function to render the preview images dynamically
  function renderPreviewImages() {
    previewImages.innerHTML = ""; // Clear previous thumbnails

    galleryImgs.forEach((img) => {
      if (img.id !== selectedGalleryImage) {
        let imgElement = document.createElement("div");
        imgElement.className =
          "w-full h-[120px] bg-cover bg-center cursor-pointer border-b border-dark";
        imgElement.style.backgroundImage = `url('${img.src}')`;
        imgElement.setAttribute("data-id", img.id);

        imgElement.addEventListener("click", function () {
          changeMainImage(parseInt(this.getAttribute("data-id")));
        });

        previewImages.appendChild(imgElement);
      }
    });
  }
  // Function to change the main image
  function changeMainImage(imgId) {
    selectedGalleryImage = imgId;
    let selectedImage = galleryImgs.find((image) => image.id === imgId);
    if (selectedImage) {
      document.getElementById("mainImage").src = selectedImage.src;
    }
    renderPreviewImages(); // Re-render the thumbnails
  }
  // Initial rendering of thumbnails
  renderPreviewImages();

  function foo(element) {
    let cartItemKey = element.dataset.cartItemKey; // No need for parseInt

    let formData = new FormData();
    formData.append("action", "remove_cart_item");
    formData.append("cart_item_key", cartItemKey);
    formData.append("nonce", cartAjax.nonce);

    // Fix: Set loading state properly
    element.textContent = "⏳";

    fetch(cartAjax.ajax_url, {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          document.getElementById("cart-total").innerText = stringToElement(
            data.data.cart_total_price
          ).textContent;
          document.getElementById("cart-total-text").innerText =
            stringToElement(data.data.cart_total_price).textContent;
          document.getElementById("cart-count").innerText =
            data.data.cart_count;
          document.getElementById("sidebar-cart-count").innerText =
            data.data.cart_count;
          document.getElementById("cart-count-desktop").innerText =
            data.data.cart_count;
          let row = element.closest("tr"); // Fix: Use element, not `this`
          getCartItems();
          if (row) {
            row.style.transition = "opacity 0.3s";
            row.style.opacity = "0";
            setTimeout(() => row.remove(), 300);
          }
          let cartCount = document.querySelector(".cart-count");
          if (cartCount) {
            cartCount.textContent = data.data.cart_count;
          }
        } else {
          alert("Error: " + (data.data?.message || "Unknown error"));
        }
      })
      .catch((error) => console.error("Error:", error));
  }

  function stringToElement(htmlString) {
    const template = document.createElement("template"); // Use <template> to avoid extra wrappers
    if (typeof htmlString === "string") template.innerHTML = htmlString.trim(); // Trim to avoid whitespace issues
    return template.content.firstChild; // Return the first element
  }

  const fullText = document.getElementById("fullText");
  const truncatedText = document.getElementById("truncatedText");
  truncatedText.innerHTML =
    fullText.textContent
      .replace("\n", "")
      .trim()
      .split("")
      .slice(0, 41)
      .join("") + "...";
  document
    .getElementById("toggleAccordion")
    .addEventListener("click", function () {
      let truncatedText = document.getElementById("truncatedText");
      let fullText = document.getElementById("fullText");
      let button = this.querySelector("span");

      if (fullText.classList.contains("hidden")) {
        fullText.classList.remove("hidden");
        truncatedText.classList.add("hidden");
      } else {
        fullText.classList.add("hidden");
        truncatedText.classList.remove("hidden");
      }
    });
  document.querySelectorAll(".update-cart-btn").forEach((item) => {
    item.addEventListener("click", quantity_changer);
  });

  document.querySelectorAll(".remove-item").forEach((item) => {
    item.addEventListener("click", foo);
  });

  document.querySelectorAll(".add-to-cart").forEach(function (button) {
    button.addEventListener("click", function () {
      let productID = this.getAttribute("data-id");
      let variationID = this.getAttribute("data-variation-id") || null;

      if (!variationID) {
        alert("Please select a variation before adding to the cart.");
        return;
      }

      let formData = new FormData();
      formData.append("action", "custom_add_to_cart");
      formData.append("product_id", productID);
      formData.append("variation_id", variationID);
      formData.append("quantity", 1);
      formData.append("nonce", wc_cart_params.nonce); // Ensure this is correctly defined

      let buttonElement = this;
      buttonElement.textContent = "Adding...";

      fetch(wc_cart_params.ajax_url, {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            document.getElementById("cart-count-desktop").textContent =
              data.data.count;
            document.getElementById("cart-count").textContent = data.data.count;
            document.getElementById("sidebar-cart-count").textContent =
              data.data.count;
            getCartItems();
            buttonElement.textContent = "Added!";
            setTimeout(() => {
              buttonElement.textContent = "Add to your cart";
            }, 2000);
          } else {
            alert("Failed to add product to cart.");
          }
        })
        .catch((error) => {
          console.error("Add to cart failed:", error);
        });
    });
  });

  let selectedVariation = null;
  document.querySelectorAll(".update-cart-btn").forEach((item) => {
    item.addEventListener("click", quantity_changer);
  });

  document.querySelectorAll(".remove-item").forEach((item) => {
    item.addEventListener("click", foo);
  });

  // Automatically select the second variation on page load
  function selectDefaultVariation() {
    let $sizeOptions = $(".size-option");
    if ($sizeOptions.length > 1) {
      let $defaultOption = $sizeOptions.eq(1); // Select the second variation
      $defaultOption.addClass("selected");
      $defaultOption.removeClass("bg-white");
      $defaultOption.removeClass("text-dark");
      $defaultOption.addClass("bg-dark");
      $defaultOption.addClass("text-white");
      selectedVariation = $defaultOption.data("id");

      let price = $defaultOption.data("price");
      $("#custom-add-to-cart")
        .data("variation-id", selectedVariation)
        .text("Add To Your Cart - $" + price);
    }
  }

  // Ensure size selection updates variation ID
  $(".size-option").on("click", function () {
    const all = $(".size-option");
    const thisOne = $(this);
    all.removeClass("selected");
    all.removeClass("bg-dark");
    all.removeClass("text-white");

    thisOne.addClass("selected");
    thisOne.removeClass("bg-white");
    thisOne.removeClass("text-dark");
    thisOne.addClass("bg-dark");
    thisOne.addClass("text-white");

    selectedVariation = $(this).data("id");
    let price = $(this).data("price");

    $("#custom-add-to-cart")
      .data("variation-id", selectedVariation)
      .text("Add To Your Cart - $" + parseFloat(price).toFixed(2));
  });

  // Automatically select the second variation when the page loads
  selectDefaultVariation();

  // Add to Cart Click Handler
  $(document).on("click", "#custom-add-to-cart", function () {
    let variationID = $(this).data("variation-id");
    let productID = $(this).data("product-id");

    if (!variationID) {
      alert("Please select a size before adding to cart.");
      return;
    }

    $.ajax({
      type: "POST",
      url: wc_cart_params.ajax_url, // Ensure this is correctly defined
      data: {
        action: "custom_add_to_cart",
        product_id: productID,
        variation_id: variationID,
        quantity: 1,
        nonce: wc_cart_params.nonce, // ✅ Security nonce
      },
      beforeSend: function () {
        document.getElementById("custom-add-to-cart").textContent = "Adding...";
      },
      success: function (response) {
        if (response.success) {
          // updateCartCount(); // ✅ Ensure cart count updates dynamically
          document.getElementById("custom-add-to-cart").textContent = "Added!";

          getCartItems();
          document.getElementById("cart-count-desktop").textContent =
            response.data.count;
          document.getElementById("cart-count").textContent =
            response.data.count;
          document.getElementById("sidebar-cart-count").textContent =
            response.data.count;
          setTimeout(function () {
            $("#custom-add-to-cart").text(
              "Add To Your Cart - $" + $(".size-option.selected").data("price")
            );
          }, 2000);
        } else {
          alert("Failed to add product to cart.");
        }
      },
      error: function (xhr, status, error) {
        console.error("Add to cart failed:", xhr.responseText);
      },
    });
  });
  getCartItems();
});
