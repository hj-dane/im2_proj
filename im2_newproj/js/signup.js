document.addEventListener("DOMContentLoaded", function() {
    let btnRegister = document.querySelector('.custom-btn');
    let users = [];

    function addUser(event) {
        event.preventDefault(); 

        var firstName = document.querySelector('.fName').value.trim();
        var lastName = document.querySelector('.lName').value.trim();
        var userType = document.querySelector('.user_type').value;
        var userEmail = document.querySelector('.email').value.trim();
        var contact = document.querySelector('.contact').value.trim();
        var newPass = document.querySelector('.password').value.trim();
        var confirmPass = document.querySelector('.confPassword').value.trim();

        if (firstName === '' || lastName === '' || userType === 'User type' || userEmail === '' || contact === '' || newPass === '' || confirmPass === '') {
            alert("All fields are required!");
            return;
        }

        if (newPass !== confirmPass) {
            alert("Passwords do not match!");
            return;
        }

        let user = {
            First_Name: firstName,
            Last_Name: lastName,
            User_Type: userType,
            User_Email: userEmail,
            Contact: contact,
            Password: newPass
        };

        users.push(user);

        console.log("Users:", users);
        alert(`${firstName} ${lastName} (${userType}) registered successfully!`);

        document.querySelector('.fName').value = '';
        document.querySelector('.lName').value = '';
        document.querySelector('.user_type').value = 'User type';
        document.querySelector('.email').value = '';
        document.querySelector('.contact').value = '';
        document.querySelector('.password').value = '';
        document.querySelector('.confPassword').value = '';
    }
    console.log("Script Loaded");
    btnRegister.addEventListener('click', addUser);
});
