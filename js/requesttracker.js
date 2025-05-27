// Wait until the DOM content is fully loaded
document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM fully loaded and parsed");

    // Function to save input values to local storage
    function saveInputValue(id, value) {
        localStorage.setItem(id, value);
    }

    // Function to clear input values and local storage
    function clearInput(id) {
        const input = document.getElementById(id);
        input.value = '';
        localStorage.removeItem(id);
        validateField(input); // Re-validate the field to update feedback
    }

    // Function to retrieve input values from local storage
    function populateInputValues() {
        document.querySelectorAll('.form-control').forEach(input => {
            const savedValue = localStorage.getItem(input.id);
            if (savedValue) {
                input.value = savedValue;
                validateField(input); // Validate the field to update feedback
            }
        });
    }

    // Function to validate input fields
    function validateField(input) {
        let isValid = false;
        const value = input.value.trim();
        const feedbackContainer = input.parentElement.nextElementSibling; // Target the feedback container
        const feedback = feedbackContainer.querySelector('.invalid-feedback, .valid-feedback');

        switch (input.id) {
            case 'reference_no':
                isValid = value.length > 0; // Ensure reference_no is not empty
                feedback.textContent = isValid ? '' : 'Please enter a reference number';
                break;
            case 'pin':
                isValid = /^[0-9]{4}$/.test(value); // Ensure PIN is exactly 4 digits
                feedback.textContent = isValid ? '' : 'Please enter a 4-digit PIN';
                break;
            // Add more cases as needed
        }

        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            feedback.classList.remove('invalid-feedback');
            feedback.classList.add('valid-feedback');
            feedback.style.display = 'block';
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            feedback.classList.remove('valid-feedback');
            feedback.classList.add('invalid-feedback');
            feedback.style.display = 'block';
        }
    }

    // Populate input values on page load
    populateInputValues();

    // Attach event listeners to inputs
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', () => validateField(input)); // Validate on input change
    });

    // Attach saveInputValue and clearInput functions to window object for global access
    window.saveInputValue = saveInputValue;
    window.clearInput = clearInput;
});


