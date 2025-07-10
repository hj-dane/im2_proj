function setupUserProfile() {
    const usernameElement = document.querySelector(".navbar .text-white");
    const profileImage = document.getElementById("profile-image");

    if (usernameElement) {
        usernameElement.textContent = "John Doe"; // Replace with actual user data
    }
    if (profileImage) {
        profileImage.src = "path/to/profile-image.jpg"; // Replace with actual image path
    }
}
