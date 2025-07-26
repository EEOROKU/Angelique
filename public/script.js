const bar = document.getElementById('bar');
const close = document.getElementById('close');
const nav = document.getElementById('navbar');

if(bar) {
    bar.addEventListener('click', () =>{
        nav.classList.add('active');
    })
}
if(close) {
    close.addEventListener('click', () =>{
        nav.classList.remove('active');
    })
}

var noti = document.querySelector('h4');
var select = document.querySelector('.select');
var button = document.getElementsByTagName('button.normal');

for (var but of button) {
    but.addEventListener('click', (e) => {
        // Increment the data-count attribute of the noti element and add the class 'zero'
        var add = Number(noti.getAttribute('data-count') || 0);
        noti.setAttribute('data-count', add + 1);
        noti.classList.add('zero');

        // Clone the item and add it to the cart
        var parent = e.target.parentNode;
        var clone = parent.cloneNode(true);
        select.appendChild(clone);
        clone.lastElementChild.innerText = "Buy-now";

        // Toggle the display of the select element when the noti element is clicked
        if (clone) {
            noti.onclick = () => {
                select.classList.toggle('display');
            }
        }
    });
}
