// User data for authentication
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
  
  // DOM elements
  const form = document.getElementById('loginForm');
  const emailInput = document.getElementById('email');
  const passwordInput = document.getElementById('password');
  const errorMessage = document.getElementById('errorMessage');
  const rememberCheckbox = document.getElementById('remember');
  
  // Form submission handler
  form?.addEventListener('submit', async (e) => {
    e.preventDefault();
  
    const email = emailInput.value.trim();
    const password = passwordInput.value.trim();
    const rememberMe = rememberCheckbox.checked;
  
    // Clear previous errors
    errorMessage.textContent = '';
    [emailInput, passwordInput].forEach(input => {
        input.parentElement.classList.remove('incorrect');
    });
  
    // Validate form
    const errors = getLoginFormErrors(email, password);
    if (errors.length > 0) {
        errorMessage.textContent = errors.join('. ');
        errorMessage.style.color = 'red';
        return;
    }
  
    // Check credentials against system users
    const systemUser = systemUsers.find(u => u.email === email && u.password === password);
  
    if (systemUser) {
        // Successful login - store system user data
        const userData = {
            systemRole: systemUser.user_role,
            ...(await fetchRandomUser()) // Merge with random user data
        };
  
        // Store user data based on "Remember me" selection
        const storage = rememberMe ? localStorage : sessionStorage;
        storage.setItem('loggedInUser', JSON.stringify(userData));
  
        // Redirect to appropriate dashboard
        redirectBasedOnRole(systemUser.user_role);
    } else {
        // Failed login
        errorMessage.style.color = 'red';
        errorMessage.textContent = 'Invalid email or password';
    }
  });
  
  // Function to fetch a random user
  async function fetchRandomUser() {
    try {
        const response = await fetch('https://randomuser.me/api/');
        const data = await response.json();
        return data.results[0];
    } catch (error) {
        console.error("Error fetching random user:", error);
        return {
            name: { first: "Guest", last: "User" },
            picture: { medium: "https://via.placeholder.com/150" },
            gender: "unknown"
        };
    }
  }
  
  // Role determination logic
  function determineUserRole(user) {
      const systemRole = user.systemRole || 'client';
      
      // Map system roles to display names
      const roleDisplayNames = {
        admin: "System Administrator",
        manager: "Inventory Manager",
        client: "Client"
      };
      
      return {
          systemRole,
          displayRole: `${roleDisplayNames[systemRole]}`
      };
  }
  
  // Update user info in the dashboard
  function updateUserInfo(user) {
      if (!user) return;
    
      const roles = determineUserRole(user);
      const fullName = `${user.name.first} ${user.name.last}`;
    
      // Update all elements with class 'name'
      document.querySelectorAll('.name').forEach(el => {
          el.textContent = fullName;
      });
    
      // Update profile picture
      const profileImg = document.querySelector('img[alt="Profile"]');
      if (profileImg) profileImg.src = user.picture.medium;
    
      // Update role display
      const roleElement = document.querySelector('.role');
      if (roleElement) roleElement.textContent = roles.displayRole;
    
      // Set data attribute for role-based styling
      document.body.dataset.userRole = roles.systemRole;
  }
  
  // Redirect based on user role
  function redirectBasedOnRole(role) {
    errorMessage.style.color = 'green';
    errorMessage.textContent = 'Login successful! Redirecting...';
    console.log(role);
    setTimeout(() => {
        switch(role) {
            case 'admin':
                window.location.href = 'html_admin/analytics.html';
                break;
            case 'manager':
                window.location.href = 'html_manager/analytics.html';
                break;
            case 'client':
                window.location.href = 'html_client/analytics.html';
                break;
            default:
                window.location.href = 'login.html';
        }
    }, 1000);
    
  }
  
  // Form validation function
  function getLoginFormErrors(email, password) {
    let errors = [];
  
    if (!email) {
        errors.push('Email is required');
        emailInput.parentElement.classList.add('incorrect');
    } else if (!/^\S+@\S+\.\S+$/.test(email)) {
        errors.push('Please enter a valid email');
        emailInput.parentElement.classList.add('incorrect');
    }
  
    if (!password) {
        errors.push('Password is required');
        passwordInput.parentElement.classList.add('incorrect');
    }
  
    return errors;
  }
  
  // Clear error styling when user types
  [emailInput, passwordInput].forEach(input => {
    input?.addEventListener('input', () => {
        if (input.parentElement.classList.contains('incorrect')) {
            input.parentElement.classList.remove('incorrect');
        }
    });
  });
  
  // On dashboard pages - load user data
  async function loadDashboardUser() {
    const storedUser = localStorage.getItem('loggedInUser') || 
                      sessionStorage.getItem('loggedInUser');
    
    if (!storedUser) {
        window.location.href = 'login.html';
        return null;
    }
  
    const user = JSON.parse(storedUser);
    updateUserInfo(user);
    return user;
  }
  
  // Initialize based on current page
  document.addEventListener("DOMContentLoaded", async () => {
    if (window.location.pathname.includes('login.html')) {
        // Check for remembered user on login page
        const loggedInUser = localStorage.getItem('loggedInUser') || 
                           sessionStorage.getItem('loggedInUser');
        if (loggedInUser) {
            rememberCheckbox.checked = true;
        }
    } else {
        // On dashboard pages
        await loadDashboardUser();
    }
  });