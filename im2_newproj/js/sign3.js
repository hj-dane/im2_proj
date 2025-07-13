const sign_in_btn = document.querySelector("#sign-in-btn");
const sign_up_btn = document.querySelector("#sign-up-btn");
const container = document.querySelector(".container");

// Sign In/Up toggle
sign_up_btn.addEventListener("click", () => {
  container.classList.add("sign-up-mode");
});

sign_in_btn.addEventListener("click", () => {
  container.classList.remove("sign-up-mode");
});

// Role dropdown toggle
const roleToggleBtn = document.querySelector(".role-toggle");
const roleMenu = document.getElementById("roleMenu");
const dropdownContainer = document.querySelector(".custom-role-dropdown");

if (roleToggleBtn && roleMenu && dropdownContainer) {
  roleToggleBtn.addEventListener("click", function (e) {
    e.preventDefault(); // Prevent form submission
    roleMenu.style.display = roleMenu.style.display === "block" ? "none" : "block";
  });

  // Close dropdown when clicking outside
  document.addEventListener("click", function (e) {
    if (!dropdownContainer.contains(e.target)) {
      roleMenu.style.display = "none";
    }
  });
}
