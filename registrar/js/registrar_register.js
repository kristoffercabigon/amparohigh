document.addEventListener("DOMContentLoaded", function() {
    console.log("DOM fully loaded and parsed");

    function saveInputValue(id, value) {
        localStorage.setItem(id, value);
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
            case 'reg_name':
                isValid = value.length > 0; 
                feedback.textContent = isValid ? '' : 'Please provide full name';
                break;
            case 'reg_email':
                if (value.length === 0) {
                    feedback.textContent = 'Please provide an email address';
                } else if (!value.includes('@')) {
                    feedback.textContent = 'Email must contain @ symbol';
                } else {
                    const domain = value.split('@')[1];
                    const validDomainRegex = /^(gmail|yahoo|outlook|hotmail|icloud|protonmail|zoho|mail|gmx|aol|fastmail|yandex|tutanota|hushmail|inbox)\.(com|net|org|info|biz|co|io|me|app|tech|store|online|xyz|edu|gov|mil)$/;
                    if (!validDomainRegex.test(domain)) {
                        feedback.textContent = 'Email must have a valid domain';
                    } else {
                        isValid = true;
                        feedback.textContent = 'Looks good';
                    }
                }
                break;
            case 'reg_password5':
                const hasMinLength = value.length >= 8;
                const hasUpperCase = /[A-Z]/.test(value);
                const hasLowerCase = /[a-z]/.test(value);
                const hasNumber = /[0-9]/.test(value);
                const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(value);
    
                if (!hasMinLength) {
                    feedback.textContent = 'Password must be at least 8 characters long';
                } else if (!hasUpperCase) {
                    feedback.textContent = 'Password must include at least one uppercase letter';
                } else if (!hasLowerCase) {
                    feedback.textContent = 'Password must include at least one lowercase letter';
                } else if (!hasNumber) {
                    feedback.textContent = 'Password must include at least one number';
                } else if (!hasSpecialChar) {
                    feedback.textContent = 'Password must include at least one special character';
                } else {
                    isValid = true;
                    feedback.textContent = 'Strong password';
                }
                break;
            case 'confirm_password':
                const regPasswordValue = document.getElementById('reg_password5').value.trim();
                if (value !== regPasswordValue) {
                    feedback.textContent = 'Passwords do not match';
                } else if (value.length === 0) {
                    feedback.textContent = 'Please confirm your password';
                } else {
                    isValid = true;
                    feedback.textContent = 'Passwords match';
                }
                break;
            case 'reg_image':
                const file = input.files[0];
                const allowedFileTypes = ['image/jpeg', 'image/png', 'image/jpg']; 
                const maxFileSize = 25 * 1024 * 1024; 
    
                if (file) {
                    if (!allowedFileTypes.includes(file.type)) {
                        feedback.textContent = 'File must be in JPG, JPEG, or PNG format';
                    } else if (file.size > maxFileSize) {
                        feedback.textContent = 'File must be less than 25MB';
                    } else {
                        isValid = true;
                        feedback.textContent = 'File looks good';
                    }
                } else {
                    feedback.textContent = 'Please upload a file';
                }
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

    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('input', () => validateField(input)); 
    });

    window.saveInputValue = saveInputValue;

    document.querySelector('form').addEventListener('submit', function(event) {
        let allValid = true;
        document.querySelectorAll('.form-control').forEach(input => {
            validateField(input);
            if (!input.classList.contains('is-valid')) {
                allValid = false;
            }
        });

        if (!allValid) {
            event.preventDefault();
        } else {
            document.getElementById('spinner').style.display = 'inline-block';
            localStorage.removeItem('reg_name');
            localStorage.removeItem('reg_email');
            localStorage.removeItem('reg_image');
            localStorage.removeItem('reg_password5');
            localStorage.removeItem('confirm_password');
        }
    });
});
