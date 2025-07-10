class Auth {
    constructor() {
        this.loginForm = document.querySelector('.sign-in-form');
        this.registerForm = document.querySelector('.sign-up-form');
        this.selectedRole = 'customer'; // Default role
        this.roleMappings = {
            'customer': 1,
            'seller': 2,
            'administrator': 3
        };
        this.init();
        this.setupRoleSelection();
    }

    init() {
        if (this.loginForm) {
            this.setupLogin();
        }
        if (this.registerForm) {
            this.setupRegister();
        }
        this.setupUI();
    }

    setupUI() {
        // Toggle between login/register forms
        document.querySelectorAll('[data-auth-toggle]').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                document.querySelector('.container').classList.toggle('sign-up-mode');
            });
        });
    }

    setupLogin() {
        this.loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = {
                username: this.loginForm.querySelector('[name="username"]').value.trim(),
                password: this.loginForm.querySelector('[name="password"]').value.trim()
            };
            
            const errors = this.validateLogin(formData);
            if (errors.length > 0) {
                this.showErrors(this.loginForm, errors);
                return;
            }
            
            try {
                const response = await this.sendRequest('/php/login.php', formData);
                
                if (response.success) {
                    this.redirectUser(response.user.role);
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
                username: this.registerForm.querySelector('[name="username"]').value.trim(),
                contact: this.registerForm.querySelector('[name="contact"]').value.trim(),
                address: this.registerForm.querySelector('[name="address"]').value.trim(),
                role: this.selectedRole,
                password: this.registerForm.querySelector('[name="password"]').value.trim(),
                confirm_password: this.registerForm.querySelector('[name="confirm_password"]').value.trim()
            };
            
            const errors = this.validateRegister(formData);
            if (errors.length > 0) return this.showErrors(this.registerForm, errors);
            
            try {
                const response = await this.sendRequest('/php/signin.php', formData);
                if (response.success) {
                    this.showSuccess(this.registerForm, response.message);
                    setTimeout(() => window.location.href = '/login.php', 1500);
                } else {
                    this.showErrors(this.registerForm, response.errors ? Object.values(response.errors) : [response.message]);
                }
            } catch (error) {
                console.error('Registration error:', error);
                this.showErrors(this.registerForm, ['An error occurred during registration']);
            }
        });
    }

    setupRoleSelection() {
        const roleMenu = document.getElementById('roleMenu');
        if (roleMenu) {
            roleMenu.querySelectorAll('li a').forEach(item => {
                item.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.selectedRole = e.target.textContent.toLowerCase();
                    document.querySelector('.role-btn').textContent = e.target.textContent;
                    roleMenu.style.display = 'none';
                });
            });
        }
    }

    validateRegister(data) {
        const errors = [];
        if (!data.username) errors.push('Username is required');
        if (!data.contact) errors.push('Contact number is required');
        if (!/^[0-9]{10,15}$/.test(data.contact)) errors.push('Invalid contact number format');
        if (!data.address) errors.push('Address is required');
        if (!['customer', 'seller', 'administrator'].includes(data.role)) errors.push('Please select a valid role');
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
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    }

    showErrors(form, messages) {
        const errorContainer = form.querySelector('.error-message') || this.createErrorContainer(form);
        errorContainer.innerHTML = messages.join('<br>');
        errorContainer.style.color = '#ff4444';
    }

    showSuccess(form, message) {
        const errorContainer = form.querySelector('.error-message') || this.createErrorContainer(form);
        errorContainer.innerHTML = message;
        errorContainer.style.color = '#00C851';
    }

    createErrorContainer(form) {
        const div = document.createElement('div');
        div.className = 'error-message';
        form.insertBefore(div, form.querySelector('button[type="submit"]').nextSibling);
        return div;
    }

    redirectUser(role) {
        const routes = {
            admin: '/admin/dashboard.php',
            seller: '/seller/analytics.html',
            customer: '/dashboard.php'
        };
        window.location.href = routes[role] || '/dashboard.php';
    }
}

document.addEventListener('DOMContentLoaded', () => new Auth());