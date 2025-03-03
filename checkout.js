window.onload = function() {
    
    const basketItems = JSON.parse(localStorage.getItem('basketItems')) || [];
    const orderList = document.getElementById('orderList');
    let totalAmount = 0;

    
    orderList.innerHTML = "";

   
    basketItems.forEach(item => {
        const listItem = document.createElement('li');
        listItem.innerHTML = `${item.name} - £${item.price} x ${item.quantity} = £${item.total}`;
        orderList.appendChild(listItem);
        totalAmount += item.total; 
    });

   
    document.getElementById('totalAmount').innerText = `Total: £${totalAmount.toFixed(2)}`;

    
};
