// User data f
const systemUsers = [
    { 
      email: "batman@gmail.com", 
      password: "batman", 
      user_role: "admin" 
    },
    { 
      email: "joker@gmail.com", 
      password: "joker", 
      user_role: "manager" 
    },
    { 
      email: "catwoman@gmail.com", 
      password: "catwoman", 
      user_role: "client" 
    }
];

const loginForm = document.querySelector('.sign-in-form');
const signupForm = document.querySelector('.sign-up-form');

function initErrorMessages() {
    if (loginForm) {
        let loginErrorMessage = loginForm.querySelector('.error-message');
        if (!loginErrorMessage) {
            loginErrorMessage = document.createElement('div');
            loginErrorMessage.className = 'error-message';
            loginForm.insertBefore(loginErrorMessage, loginForm.querySelector('p'));
        }
    }
    if (signupForm) {
        // Check if error message already exists
        let signupErrorMessage = signupForm.querySelector('.error-message');
        if (!signupErrorMessage) {
            signupErrorMessage = document.createElement('div');
            signupErrorMessage.className = 'error-message';
            signupForm.insertBefore(signupErrorMessage, signupForm.querySelector('p'));
        }
    }
}

// Login 
function setupLogin() {
    if (!loginForm) {
        console.error('Login form not found!');
        return;
    }
    
    const loginUsernameInput = loginForm.querySelector('input[type="text"], input[type="email"]');
    const loginPasswordInput = loginForm.querySelector('input[type="password"]');
    const loginErrorMessage = loginForm.querySelector('.error-message');

    if (!loginUsernameInput || !loginPasswordInput) {
        console.error('Login inputs not found!');
        return;
    }

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const input = loginUsernameInput.value.trim();
        const password = loginPasswordInput.value.trim();

        loginErrorMessage.textContent = '';
        [loginUsernameInput, loginPasswordInput].forEach(input => {
            input.parentElement.classList.remove('incorrect');
        });

        if (!input || !password) {
            loginErrorMessage.textContent = 'Both fields are required';
            loginErrorMessage.style.color = 'red';
            return;
        }

        const user = systemUsers.find(u => {
            const isEmail = input.includes('@');
            const emailMatch = isEmail ? u.email === input : u.email === `${input}@gmail.com`;
            return emailMatch && u.password === password;
        });

        if (user) {
            console.log(`Login success: ${user.email} as ${user.user_role}`);
            
            try {
                const randomUser = await fetchRandomUser();
                const userData = {
                    email: user.email,
                    role: user.user_role,
                    name: `${randomUser.name.first} ${randomUser.name.last}`,
                    picture: randomUser.picture.medium
                };

                sessionStorage.setItem('currentUser', JSON.stringify(userData));
                console.log('Session storage after set:', sessionStorage.getItem('currentUser'));
                redirectBasedOnRole(user.user_role);
            } catch (error) {
                console.error('Error during login:', error);
                loginErrorMessage.textContent = 'Error during login. Please try again.';
                loginErrorMessage.style.color = 'red';
            }
        } else {
            loginErrorMessage.textContent = 'Invalid username or password';
            loginErrorMessage.style.color = 'red';
        }
    });
}


// Signup 
function setupSignup() {
    if (!signupForm) return;

    const signupUsernameInput = signupForm.querySelector('input[type="text"]');
    const signupEmailInput = signupForm.querySelector('input[type="email"]');
    const signupPasswordInput = signupForm.querySelector('input[type="password"]');
    const signupErrorMessage = signupForm.querySelector('.error-message');

    signupForm.addEventListener('submit', (e) => {
        e.preventDefault();
        
        const username = signupUsernameInput.value.trim();
        const email = signupEmailInput.value.trim();
        const password = signupPasswordInput.value.trim();

        signupErrorMessage.textContent = '';
        [signupUsernameInput, signupEmailInput, signupPasswordInput].forEach(input => {
            input.parentElement.classList.remove('incorrect');
        });

        const errors = getSignupFormErrors(username, email, password);
        if (errors.length > 0) {
            signupErrorMessage.textContent = errors.join('. ');
            signupErrorMessage.style.color = 'red';
            return;
        }

        signupErrorMessage.style.color = "green";
        signupErrorMessage.textContent = "Registration successful! Redirecting to login...";

        setTimeout(() => {
            if (container) container.classList.remove("sign-up-mode");
            if (loginForm) {
                const loginUsername = loginForm.querySelector('input[type="text"]');
                if (loginUsername) loginUsername.value = username;
            }
        }, 1500);
    });
}

async function fetchRandomUser() {
    try {
        const response = await fetch('https://randomuser.me/api/');
    
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const data = await response.json();
        
        if (!data.results || !data.results[0]) {
            throw new Error('Invalid user data received');
        }
        
        
        return data.results[0];

    
    } catch (error) {
        console.error("Error fetching random user:", error);
        // Return a consistent fallback structure
        return {
            name: { first: "Guest", last: "User" },
            picture: { medium: "https://via.placeholder.com/150" }
        };
    }
}


function determineUserRole(user) {
    const systemRole = user.systemRole || 'client';
    
    const roleDisplayNames = {
        admin: "System Administrator",
        manager: "Inventory Manager",
        client: "Client"
    };
    
    return {
        systemRole,
        displayRole: roleDisplayNames[systemRole] || "User"
    };
}

function updateUserInfo(user) {
    console.log('Updating user info with:', user); // Debug log
    if (!user) {
        console.warn('No user provided to updateUserInfo');
        return;
    }
  
    const roles = determineUserRole(user);
    const fullName = `${user.name.first} ${user.name.last}`;
    
    const nameElements = document.querySelectorAll('.name');
    console.log(`Found ${nameElements.length} name elements`); // Debug log
    
    nameElements.forEach(el => {
        el.textContent = fullName;
    });
  
    const profileImg = document.querySelector('img[alt="Profile"]');
    console.log('Profile img element:', profileImg); 
    if (profileImg && user.picture) {
        profileImg.src = user.picture.medium;
    }
  
    const roleElement = document.querySelector('.role');
    console.log('Role element:', roleElement); 
    if (roleElement) roleElement.textContent = roles.displayRole;
  
    document.body.dataset.userRole = roles.systemRole;
}

function redirectBasedOnRole(role) {
    console.log(`Redirecting as ${role}`);
    
    const routes = {
        admin: 'html_admin/analytics.html',  // Added leading slash
        manager: 'html_manager/analytics.html',
        client: 'html_client/analytics.html'
    };

    const path = routes[role] || '/index.html';
    console.log(`Redirecting to: ${path}`);

    setTimeout(() => {
        window.location.href = routes[role] || 'index.html';
    }, 1000);
}

function getLoginFormErrors(username, password) {
    let errors = [];
  
    if (!username) {
        errors.push('Username is required');
        loginUsernameInput.parentElement.classList.add('incorrect');
    }
  
    if (!password) {
        errors.push('Password is required');
        loginPasswordInput.parentElement.classList.add('incorrect');
    } else if (password.length < 4) {
        errors.push('Password must be at least 4 characters');
        loginPasswordInput.parentElement.classList.add('incorrect');
    }
  
    return errors;
}

function getSignupFormErrors(username, email, password) {
    let errors = [];
    const signupUsernameInput = signupForm?.querySelector('input[type="text"]');
    const signupEmailInput = signupForm?.querySelector('input[type="email"]');
    const signupPasswordInput = signupForm?.querySelector('input[type="password"]');

    if (!username) {
        errors.push('Username is required');
        if (signupUsernameInput) signupUsernameInput.parentElement.classList.add('incorrect');
    }

    if (!email) {
        errors.push('Email is required');
        if (signupEmailInput) signupEmailInput.parentElement.classList.add('incorrect');
    } else if (!/^\S+@\S+\.\S+$/.test(email)) {
        errors.push('Please enter a valid email');
        if (signupEmailInput) signupEmailInput.parentElement.classList.add('incorrect');
    }

    if (!password) {
        errors.push('Password is required');
        if (signupPasswordInput) signupPasswordInput.parentElement.classList.add('incorrect');
    } else if (password.length < 6) {
        errors.push('Password must be at least 6 characters');
        if (signupPasswordInput) signupPasswordInput.parentElement.classList.add('incorrect');
    }

    return errors;
}

// [loginUsernameInput, loginPasswordInput].forEach(input => {
//     input?.addEventListener('input', () => {
//         if (input.parentElement.classList.contains('incorrect')) {
//             input.parentElement.classList.remove('incorrect');
//         }
//     });
// });

// [signupUsernameInput, signupEmailInput, signupPasswordInput].forEach(input => {
//     input?.addEventListener('input', () => {
//         if (input.parentElement.classList.contains('incorrect')) {
//             input.parentElement.classList.remove('incorrect');
//         }
//     });
// });

async function loadDashboardUser() {
    try {
        const storedUser = sessionStorage.getItem('currentUser');
        console.log('Session storage content:', storedUser);
        
        if (!storedUser) {
            console.log('No user found in sessionStorage, redirecting to index');
            window.location.href = 'index.html';
            return null;
        }
  
        const user = JSON.parse(storedUser);
        console.log('Loaded user from sessionStorage:', user);
        
        // Create properly structured user object
        const nameParts = user.name.split(' ');
        const userData = {
            name: {
                first: nameParts[0],
                last: nameParts.slice(1).join(' ') || ''
            },
            picture: { 
                medium: user.picture 
            },
            systemRole: user.role
        };
        
        console.log('Processed user data:', userData);
        updateUserInfo(userData);
        return user;
    } catch (error) {
        console.error('Error loading dashboard user:', error);
        window.location.href = 'index.html';
        return null;
    }
}

function checkUserRole() {
    try {
        const storedUser = sessionStorage.getItem('currentUser');
        if (!storedUser) {
            console.log('No user session found, redirecting to sign.html');
            window.location.href = 'sign.html';
            return null;
        }

        const userData = JSON.parse(storedUser);
        console.log('Current user role:', userData.role);
        
        document.body.dataset.userRole = userData.role;
        
        const roleElement = document.querySelector('.role');
        if (roleElement) {
            const roleNames = {
                admin: "System Administrator",
                manager: "Inventory Manager",
                client: "Client"
            };
            roleElement.textContent = roleNames[userData.role] || "User";
        }
        
        return userData.role;
    } catch (error) {
        console.error('Error checking user role:', error);
        window.location.href = 'sign.html';
        return null;
    }
}

function logout() {
    sessionStorage.removeItem('currentUser');
    window.location.href = 'login.html';
}

// Initialize the application
function initApp() {
    console.log('Initializing application...');
    initErrorMessages();
    setupLogin();
    setupSignup();

    // Only run these on dashboard pages
    if (document.querySelector('.dashboard')) {
        loadDashboardUser();
        checkUserRole();
    }
}

// Start the application when DOM is ready
document.addEventListener('DOMContentLoaded', initApp);