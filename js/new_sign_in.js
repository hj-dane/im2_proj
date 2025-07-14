class Auth {
    constructor() {
        this.loginForm = document.querySelector('.sign-in-form');
        this.registerForm = document.querySelector('.sign-up-form');
        this.init();
        this.setupUI();
    }

    init() {
        if (this.loginForm) {
            this.setupLogin();
        }
        if (this.registerForm) {
            this.setupRegister();
        }
    }

    setupUI() {
        // Toggle between login/register forms
        const sign_in_btn = document.querySelector("#sign-in-btn");
        const sign_up_btn = document.querySelector("#sign-up-btn");
        const container = document.querySelector(".container");

        if (sign_up_btn) {
            sign_up_btn.addEventListener("click", (e) => {
                e.preventDefault();
                container.classList.add("sign-up-mode");
            });
        }

        if (sign_in_btn) {
            sign_in_btn.addEventListener("click", (e) => {
                e.preventDefault();
                container.classList.remove("sign-up-mode");
            });
        }
    }

    setupLogin() {
        this.loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = {
                login: true,
                username: this.loginForm.querySelector('[name="username"]').value.trim(),
                password: this.loginForm.querySelector('[name="password"]').value.trim()
            };
            
            const errors = this.validateLogin(formData);
            if (errors.length > 0) {
                this.showErrors(this.loginForm, errors);
                return;
            }
            
            try {
                const response = await this.sendRequest(window.location.href, formData);
                
                if (response.success) {
                    if (response.redirect) {
                        window.location.href = response.redirect;
                    } else {
                        window.location.href = '/dashboard.php'; // Default redirect
                    }
                } else {
                    this.showErrors(this.loginForm, [response.message]);
                }
            } catch (error) {
                console.error('Login error:', error);
                this.showErrors(this.loginForm, ['An error occurred during login']);
            }
        });
    }

    setupRegister() {
        this.registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = {
                signup: true,
                username: this.registerForm.querySelector('[name="username"]').value.trim(),
                contact: this.registerForm.querySelector('[name="contact"]').value.trim(),
                address: this.registerForm.querySelector('[name="address"]').value.trim(),
                password: this.registerForm.querySelector('[name="password"]').value.trim(),
                confirm_password: this.registerForm.querySelector('[name="confirm_password"]').value.trim()
            };
            
            const errors = this.validateRegister(formData);
            if (errors.length > 0) return this.showErrors(this.registerForm, errors);
            
            try {
                const response = await this.sendRequest(window.location.href, formData);
                if (response.success) {
                    this.showSuccess(this.registerForm, response.message);
                    setTimeout(() => {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            window.location.href = '/login.php';
                        }
                    }, 1500);
                } else {
                    this.showErrors(this.registerForm, response.errors ? Object.values(response.errors) : [response.message]);
                }
            } catch (error) {
                console.error('Registration error:', error);
                this.showErrors(this.registerForm, ['An error occurred during registration']);
            }
        });
    }

    validateRegister(data) {
        const errors = [];
        if (!data.username) errors.push('Username is required');
        if (!data.contact) errors.push('Contact number is required');
        if (!/^[0-9]{10,15}$/.test(data.contact)) errors.push('Invalid contact number format');
        if (!data.address) errors.push('Address is required');
        if (!data.password) errors.push('Password is required');
        if (data.password.length < 6) errors.push('Password must be at least 6 characters');
        if (data.password !== data.confirm_password) errors.push('Passwords do not match');
        return errors;
    }

    validateLogin(data) {
        const errors = [];
        if (!data.username) errors.push('Username is required');
        if (!data.password) errors.push('Password is required');
        return errors;
    }

    async sendRequest(url, data) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    }

    showErrors(form, messages) {
        const errorContainer = form.querySelector('.error-message') || this.createMessageContainer(form, 'error-message');
        errorContainer.innerHTML = messages.join('<br>');
        errorContainer.style.color = '#dc3545';
        errorContainer.style.display = 'block';
        
        // Clear success message if exists
        const successContainer = form.querySelector('.success-message');
        if (successContainer) {
            successContainer.style.display = 'none';
        }
    }

    showSuccess(form, message) {
        const successContainer = form.querySelector('.success-message') || this.createMessageContainer(form, 'success-message');
        successContainer.innerHTML = message;
        successContainer.style.color = '#28a745';
        successContainer.style.display = 'block';
        
        // Clear error message if exists
        const errorContainer = form.querySelector('.error-message');
        if (errorContainer) {
            errorContainer.style.display = 'none';
        }
    }

    createMessageContainer(form, className) {
        const div = document.createElement('div');
        div.className = className;
        form.appendChild(div);
        return div;
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => new Auth());