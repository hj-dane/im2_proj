const form = document.getElementById('loginForm');
const email_input = document.getElementById('email');
const password_input = document.getElementById('password');
const error_message = document.getElementById('errorMessage');  

// const validEmail = "batman@gmail.com"; 
// const validPassword = "batman"; 

form.addEventListener('submit', (e) => {
    e.preventDefault(); 

    let error = [];

    const email = email_input.value.trim();
    const password = password_input.value.trim();

    error = getLoginFormErrors(email, password);

    if (error.length > 0) {
        error_message.innerText = error.join(". ");
        error_message.style.color = "red";
    } else {
        if (email === validEmail && password === validPassword) {
            error_message.style.color = "green";
            error_message.innerText = "Login successful! Redirecting to dashboard...";

            setTimeout(() => {
                window.location.href = "analytics.html";
            }, 2000);
        } else {
            error_message.style.color = "red";
            error_message.innerText = "Account does not exist or incorrect credentials.";
        }
    }
});

function getLoginFormErrors(email, password) {
    let errors = [];

    if (email === '' || email == null) {
        errors.push('Email is required');
        email_input.parentElement.classList.add('incorrect');
    }

    if (password === '' || password == null) {
        errors.push('Password is required');
        password_input.parentElement.classList.add('incorrect');
    }

    return errors;
}

[email_input, password_input].forEach(input => {
    input.addEventListener('input', () => {
        if (input.parentElement.classList.contains('incorrect')) {
            input.parentElement.classList.remove('incorrect');
        }
    });
});
