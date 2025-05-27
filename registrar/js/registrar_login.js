document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM fully loaded and parsed");

    function saveInputValue(id, value) {
        localStorage.setItem(id, value);
    }

    function clearInput(id) {
        const input = document.getElementById(id);
        input.value = '';
        localStorage.removeItem(id);
        validateField(input); 
    }

    function populateInputValues() {
        document.querySelectorAll('.form-control').forEach(input => {
            const savedValue = localStorage.getItem(input.id);
            if (savedValue) {
                input.value = savedValue;
                validateField(input); 
            }
        });
    }

    function validateField(input) {
        let isValid = false;
        const value = input.value.trim();
        const feedbackContainer = input.parentElement.nextElementSibling; 
        const feedback = feedbackContainer.querySelector('.invalid-feedback, .valid-feedback');

        switch (input.id) {
            case 'log_regemail':
                if (value.length === 0) {
                    feedback.textContent = 'Please provide your email address';
                } else if (!value.includes('@')) {
                    feedback.textContent = 'Email must contain @ symbol';
                } else {
                    const domain = value.split('@')[1];
                    const validDomainRegex = /^(gmail|yahoo|outlook|hotmail|icloud|protonmail|zoho|mail|gmx|aol|fastmail|yandex|tutanota|hushmail|inbox)\.(com|net|org|info|biz|co|io|me|app|tech|store|online|xyz|edu|gov|mil)$/;
                    if (!validDomainRegex.test(domain)) {
                        feedback.textContent = 'Email must have a valid domain';
                    } else {
                        isValid = true;
                        feedback.textContent = '';
                    }
                }
                break;
            case 'log_regpassword':
                isValid = value.length > 0;
                feedback.textContent = isValid ? '' : 'Please provide your password';
                break;
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

    populateInputValues();

    // Attach event listeners to inputs
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', () => validateField(input)); 
    });

    window.saveInputValue = saveInputValue;
    window.clearInput = clearInput;
});


