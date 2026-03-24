document.addEventListener('DOMContentLoaded', () => {
    const qtyButtons = document.querySelectorAll('.qty-btn');

    qtyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const plantId = this.getAttribute('data-id');
            const action = this.innerText === '+' ? 'increase' : 'decrease';
            const qtySpan = this.parentElement.querySelector('.qty-number');
            let currentQty = parseInt(qtySpan.innerText);

            // Prevent going below 1 (unless you want to auto-remove)
            if (action === 'decrease' && currentQty <= 1) return;

            fetch('../server/cart_controller.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `plant_id=${plantId}&action=${action}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // 1. Update the number on the screen
                    qtySpan.innerText = data.new_qty;
                    // 2. Refresh the page totals (or update them via JS)
                    // a quick location.reload() is the easiest way 
                    // to update all subtotals at once:
                    location.reload(); 
                }
            });
        });
    });
});


document.querySelectorAll('.remove-btn').forEach(button => {
    button.addEventListener('click', function() {
        const plantId = this.getAttribute('data-id');
        
        fetch('../server/cart_controller.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `plant_id=${plantId}&action=remove`
        })
        .then(() => location.reload()); // Refresh to show item is gone
    });
});
