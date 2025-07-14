// Get URL parameters for MikroTik integration
function getUrlParams() {
    const params = new URLSearchParams(window.location.search);
    return {
        dst: params.get('dst') || '',
        popup: params.get('popup') || 'true'
    };
}

// Initialize form with MikroTik parameters
function initializeForm() {
    const params = getUrlParams();
    document.querySelector('input[name="dst"]').value = params.dst;
    document.querySelector('input[name="popup"]').value = params.popup;
}

// Login form submission handler
function doLogin() {
    const cardNumber = document.getElementById('cardNumber').value.trim();
    const spinner = document.getElementById('spinner');
    const statusMessage = document.getElementById('statusMessage');
    const submitBtn = document.querySelector('.login-btn');
    
    // Reset status
    statusMessage.style.display = 'none';
    statusMessage.className = 'status-message';
    
    // Validate card number
    if (!cardNumber) {
        showMessage('يرجى إدخال رقم البطاقة', 'error');
        return false;
    }
    
    if (cardNumber.length < 4) {
        showMessage('رقم البطاقة قصير جداً', 'error');
        return false;
    }
    
    // Show loading state
    spinner.style.display = 'block';
    submitBtn.disabled = true;
    submitBtn.querySelector('span').textContent = 'جاري الاتصال...';
    
    // Optional: Validate card via AJAX before submitting to MikroTik
    if (window.validateCard) {
        validateCardNumber(cardNumber)
            .then(isValid => {
                if (!isValid) {
                    showMessage('رقم البطاقة غير صحيح أو منتهي الصلاحية', 'error');
                    resetButton();
                    return false;
                }
                return true;
            })
            .catch(() => {
                // If validation fails, proceed anyway
                return true;
            });
    }
    
    // Allow form submission to MikroTik
    return true;
}

// Show status message
function showMessage(message, type) {
    const statusMessage = document.getElementById('statusMessage');
    statusMessage.textContent = message;
    statusMessage.className = `status-message ${type}`;
    statusMessage.style.display = 'block';
}

// Reset button state
function resetButton() {
    const spinner = document.getElementById('spinner');
    const submitBtn = document.querySelector('.login-btn');
    
    spinner.style.display = 'none';
    submitBtn.disabled = false;
    submitBtn.querySelector('span').textContent = 'دخول';
}

// Optional card validation function
async function validateCardNumber(cardNumber) {
    try {
        const response = await fetch('validate.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ cardNumber })
        });
        
        const result = await response.json();
        return result.valid;
    } catch (error) {
        console.log('Card validation unavailable, proceeding...');
        return true;
    }
}

// Handle form errors
window.addEventListener('load', function() {
    initializeForm();
    
    // Check for error parameters from MikroTik
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    
    if (error) {
        let errorMessage = 'خطأ في تسجيل الدخول';
        switch(error) {
            case 'invalid-user':
                errorMessage = 'رقم البطاقة غير صحيح';
                break;
            case 'expired':
                errorMessage = 'انتهت صلاحية البطاقة';
                break;
            case 'used':
                errorMessage = 'تم استخدام البطاقة من قبل';
                break;
        }
        showMessage(errorMessage, 'error');
    }
});

// Auto-focus on card input
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('cardNumber').focus();
});