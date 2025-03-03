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
        document.getElementById(`cart-item-${cartItemKey}-quantity`).innerText =
          data.data.new_quantity;
        document.getElementById("cart-total").innerText = stringToElement(
          data.data.cart_total
        ).textContent;
        document.getElementById("cart-total-text").innerText = stringToElement(
          data.data.cart_total
        ).textContent;
        document.getElementById("cart-count").innerText = data.data.cart_count;
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
  function stringToElement(htmlString) {
    const template = document.createElement("template"); // Use <template> to avoid extra wrappers
    if (typeof htmlString === "string") template.innerHTML = htmlString.trim(); // Trim to avoid whitespace issues
    return template.content.firstChild; // Return the first element
  }
}
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
        document.getElementById("cart-total-text").innerText = stringToElement(
          data.data.cart_total_price
        ).textContent;
        document.getElementById("cart-count").innerText = data.data.cart_count;
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

function getCartItems() {
  fetch(ajax_object.ajax_url + "?action=get_cart_items")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        console.log("data.data: ", data.data);
        let c = 0;
        if (data.data.cart_items.length > 0) {
          c = data.data.cart_items[0].count;
        }
        document.getElementById("cart-count").innerText = c;
        document.getElementById("sidebar-cart-count").innerText = c;
        document.getElementById("cart-count-desktop").innerText = c;
        renderCartItems(data.data.cart_items);
        refreshNonce();
      } else {
        console.error("Error fetching cart items:", data);
      }
    })
    .catch((error) => console.error("AJAX Error:", error));
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
