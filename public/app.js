let listProductHTML = document.querySelector('.listProduct');
let featProductHTML = document.querySelector('.featProduct');
let listCartHTML = document.querySelector('.listCart');
let iconCart = document.querySelector('.icon-cart');
let iconCartSpan = document.querySelector('.icon-cart span');
let body = document.querySelector('body');
let closeCart = document.querySelector('.close');
let products = [];
let cart = [];
let fProducts = [];
let nProducts = [];
iconCart.addEventListener('click', () => {
    body.classList.toggle('showCart');
})
closeCart.addEventListener('click', () => {
    body.classList.toggle('showCart');
})

    const addDataToHTML = () => {
    // remove datas default from HTML

        // add new datas
        if(products.length > 0) // if has data
        {
            products.forEach(product => {
                let newProduct = document.createElement('div');
                newProduct.dataset.id = product.id;
                newProduct.classList.add('pro');
                newProduct.innerHTML = 
                `<img src="img/products/${product.img}" alt="">
                <div class="des">
                    <span>${product.brand}</span>
                    <h5>${product.name}</h5>
                <h4>N${product.price}</h4>
                </div>
                <button class="addCart">Add To Cart</button>`;
                listProductHTML.appendChild(newProduct);
            });
        }
    }

    const addFeaturedToHTML = () => {

        if(products.length > 0){
            products.forEach(product =>{
                let newProduct = document.createElement('div');
                newProduct.dataset.id = product.id;
                newProduct.classList.add('item');
                newProduct.innerHTML =  
                `<label class="container">
                    <p>${product.name}</p>
                    <input type="checkbox" >
                    <span class="checkmark"></span>
                </label>`;
                featProductHTML.appendChild(newProduct);
            });
        }
    }
    
    listProductHTML.addEventListener('click', (event) => {
        let positionClick = event.target;
        if(positionClick.classList.contains('addCart')){
            let id_product = positionClick.parentElement.dataset.id;
            addToCart(id_product);
        }
    })

    const showNotification = (message) => {
        const notification = document.getElementById('notification');
        notification.textContent = message; // Set the custom message
        notification.classList.add('show'); // Add the `show` class to make it visible
    
        // Remove the notification after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
        }, 3000);
    };

    
const addToCart = (product_id) => {
    let positionThisProductInCart = cart.findIndex((value) => value.product_id == product_id);
    if(cart.length <= 0){
        cart = [{
            product_id: product_id,
            quantity: 1
        }];
    }else if(positionThisProductInCart < 0){
        cart.push({
            product_id: product_id,
            quantity: 1
        });
    }else{
        cart[positionThisProductInCart].quantity = cart[positionThisProductInCart].quantity + 1;
    }
    addCartToHTML();
    addCartToMemory();
    // Show success notification
    showNotification("Product added to cart successfully!");
}
const addCartToMemory = () => {
    localStorage.setItem('cart', JSON.stringify(cart));
}
const addCartToHTML = () => {
    listCartHTML.innerHTML = '';
    let totalQuantity = 0;
    let TotalPrice = 0;
    if(cart.length > 0){
        cart.forEach(item => {
            totalQuantity = totalQuantity +  item.quantity;
            let newItem = document.createElement('div');
            newItem.classList.add('item');
            newItem.dataset.id = item.product_id;

            let positionProduct = products.findIndex((value) => value.id == item.product_id);
            let info = products[positionProduct];
            listCartHTML.appendChild(newItem);
            newItem.innerHTML = `
            <div class="image">
                    <img src="img/products/${info.img}">
                </div>
                <div class="name">
                ${info.name}
                </div>
                <div class="totalPrice">N${info.price * item.quantity}</div>
                <div class="quantity">
                    <span class="minus"><</span>
                    <span>${item.quantity}</span>
                    <span class="plus">></span>
                </div>
            `;
            let productPrice = info.price * item.quantity;
            TotalPrice = TotalPrice + productPrice;
            
        })
    }
    iconCartSpan.innerText = totalQuantity;
    
     // Update or create the cartTotal element
     let cartTotalElement = document.querySelector('.cartTotal');
     if (!cartTotalElement) {
         cartTotalElement = document.createElement('div');
         cartTotalElement.classList.add('cartTotal');
         listCartHTML.parentNode.insertBefore(cartTotalElement, listCartHTML.nextSibling); // Insert after listCart
     }
 
     cartTotalElement.innerHTML = `
         <span>Total Price: </span>
         <span>N${TotalPrice.toFixed(2)}</span>
     `;
 
     console.log(TotalPrice);
}

listCartHTML.addEventListener('click', (event) => {
    let positionClick = event.target;
    if(positionClick.classList.contains('minus') || positionClick.classList.contains('plus')){
        let product_id = positionClick.parentElement.parentElement.dataset.id;
        let type = 'minus';
        if(positionClick.classList.contains('plus')){
            type = 'plus';
        }
        changeQuantityCart(product_id, type);
    }
})
const changeQuantityCart = (product_id, type) => {
    let positionItemInCart = cart.findIndex((value) => value.product_id == product_id);
    if(positionItemInCart >= 0){
        let info = cart[positionItemInCart];
        switch (type) {
            case 'plus':
                cart[positionItemInCart].quantity = cart[positionItemInCart].quantity + 1;
                break;
        
            default:
                let changeQuantity = cart[positionItemInCart].quantity - 1;
                if (changeQuantity > 0) {
                    cart[positionItemInCart].quantity = changeQuantity;
                }else{
                    cart.splice(positionItemInCart, 1);
                }
                break;
        }
    }
    addCartToHTML();
    addCartToMemory();
}

document.querySelector('form').addEventListener('submit', (event) => {


    // Send cart data to PHP
    fetch('/checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(cart), // Send the cart as JSON
    })
        .then(response => response.json())
        .then(data => {
            if (data.url) {
                window.location.href = data.url; // Redirect to Stripe Checkout
            } else {
                console.error('Error:', data.error);
                alert('Failed to start checkout: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An unexpected error occurred.');
        });
    
});

const initApp = () => {
    // get data product
    fetch('products.json')
    .then(response => response.json())
    .then(data => {
        products = data;
        addDataToHTML();
        addFeaturedToHTML();

        // get data cart from memory
        if(localStorage.getItem('cart')){
            cart = JSON.parse(localStorage.getItem('cart'));
            addCartToHTML();
        }
    })
}
initApp(); 