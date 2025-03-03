document.addEventListener('DOMContentLoaded', function() {

function updateCartTotal() {
    var cartItemContainer = document.getElementsByClassName('basket-items')[0]

    if (!cartItemContainer) return;
    
    var items = cartItemContainer.getElementsByClassName('basket-item')
    var total = 0
   
    var basketItems = [];

    for (var i = 0; i < items.length; i++) {
        var item = items[i]
        var priceElement = item.getElementsByClassName('item-price')[0]
        var quantityElement = item.getElementsByClassName('cart-quantity-input')[0]
        var price = parseFloat(priceElement.innerText.replace('Price: £', ''))
        var quantity = parseInt(quantityElement.value, 10)

        // Note to self, gonna have to mess around with this one, keeps adding twice the price x
        //Error taking initial input I imagine x
        total = total + (price * quantity)

        //Nevermind, fixed that near insantly. += instead of =.

        basketItems.push({
                name: item.getElementsByClassName('item-name')[0].innerText,
                price: price,
                quantity: quantity,
                total: price * quantity
            });
        

}
total = Math.round(total * 100) / 100 // rounds to 2 decimals
var totalElements = document.getElementsByClassName('total');
if (totalElements.length > 0) {
    totalElements[0].innerText = 'Total: £' + total;
}

localStorage.setItem('basketItems', JSON.stringify(basketItems));




function ready(){
    var removeCartItemButtons = document.getElementsByClassName('btn-danger')
    for (var i = 0; i < removeCartItemButtons.length; i++) {
        var button = removeCartItemButtons[i]
        button.addEventListener('click', removeItem)
    }

    var quantityInputs = document.getElementsByClassName('cart-quantity-input')
    for (var i = 0; i < quantityInputs.length; i++) {
        var input = quantityInputs[i]
        input.addEventListener('change', quantityChange)
    }

    //updating the total
    updateCartTotal();
}


function removeItem(event) {
    var buttonClicked = event.target
    buttonClicked.parentElement.remove()
    updateCartTotal()

}

function quantityChange(event) {
    var input = event.target 
    if (isNaN(input.value) || input.value <= 0) {
        input.value = 1
    }
    updateCartTotal()
}

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', ready);
    } else {
        ready();
    }
  
    });
