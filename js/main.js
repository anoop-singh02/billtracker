// ------------------------------------------------------------
// AJAX functions for bill management
// ------------------------------------------------------------

/**
 * toggleBillStatus - Sends an AJAX request to toggle the paid/unpaid status
 * of a specific bill and updates the UI without reloading the page.
 *
 * @param {number} billId - The unique ID of the bill to update.
 */
function toggleBillStatus(billId) {
    fetch('api/toggle_bill_status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ billId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Locate the bill card element by data attribute
            const billElement = document.querySelector(`[data-bill-id="${billId}"]`);
            const statusBadge = billElement.querySelector('.status-badge');
            const newStatus = data.status; // 'paid' or 'unpaid'
            
            // Update badge text and styling based on new status
            statusBadge.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            statusBadge.className = `status-badge ${newStatus === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}`;
        }
    })
    .catch(error => console.error('Error toggling bill status:', error));
}

// ------------------------------------------------------------
// Client-side form validation for adding/editing bills
// ------------------------------------------------------------

/**
 * validateBillForm - Checks required fields and returns false if any validation fails.
 * Shows alert messages for user feedback.
 *
 * @param {HTMLFormElement} form - The form element containing bill inputs.
 * @returns {boolean} - True if all inputs are valid; false otherwise.
 */
function validateBillForm(form) {
    const title = form.querySelector('[name="title"]').value;
    const amount = parseFloat(form.querySelector('[name="amount"]').value);
    const dueDate = form.querySelector('[name="due_date"]').value;
    const category = form.querySelector('[name="category"]').value;
    
    // Title must be non-empty
    if (!title || title.trim() === '') {
        alert('Please enter a bill title');
        return false;
    }
    
    // Amount must be a positive number
    if (isNaN(amount) || amount <= 0) {
        alert('Please enter a valid amount');
        return false;
    }
    
    // Due date must be selected
    if (!dueDate) {
        alert('Please select a due date');
        return false;
    }
    
    // Category must be chosen
    if (!category) {
        alert('Please select a category');
        return false;
    }
    
    return true;
}

// ------------------------------------------------------------
// Initialize date inputs to today's date if empty
// ------------------------------------------------------------

document.addEventListener('DOMContentLoaded', function() {
    // Find all date-type inputs on the page
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        // If no value is set, default to today
        if (!input.value) {
            input.valueAsDate = new Date();
        }
    });
});
