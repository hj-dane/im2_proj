// Function to fetch a random user
async function fetchRandomUser() {
    const response = await fetch('https://randomuser.me/api/');
    const data = await response.json();
    return data.results[0];
}

function updateUserInfo(user) {
    const nameElements = document.querySelectorAll('.name');
    const fullName = `${user.name.first} ${user.name.last}`;
    
    nameElements.forEach(element => {
        element.textContent = fullName;
    });
    
    // Update profile picture
    document.querySelector('img[alt="Profile"]').src = user.picture.medium;
    
    // Update role (you can customize this logic)
    const roleElement = document.querySelector('.role');
    roleElement.textContent = determineUserRole(user);
}

// Example role determination
function determineUserRole(user) {
    return user.gender === 'male' ? 'Inventory Manager' : 'Sales Administrator';
}

document.addEventListener("DOMContentLoaded", async () => {
    let user;
    const storedUser = localStorage.getItem('user');

    if (storedUser) {
        user = JSON.parse(storedUser);
    } else {
        user = await fetchRandomUser();
        localStorage.setItem('user', JSON.stringify(user));
    }

    updateUserInfo(user);
});
