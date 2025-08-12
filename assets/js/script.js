// assets/js/script.js

document.addEventListener("DOMContentLoaded", function () {

    /** ===== Fade in the login card ===== **/
    const card = document.querySelector(".card");
    if (card) {
        card.style.opacity = "0";
        setTimeout(() => {
            card.style.transition = "opacity 0.8s ease-in-out";
            card.style.opacity = "1";
        }, 100);
    }

    /** ===== Password Toggle ===== **/
    const togglePassword = document.querySelector("#togglePassword");
    const passwordField = document.querySelector("#password");

    if (togglePassword && passwordField) {
        togglePassword.addEventListener("click", () => {
            const type =
                passwordField.getAttribute("type") === "password"
                    ? "text"
                    : "password";
            passwordField.setAttribute("type", type);

            togglePassword.classList.toggle("bi-eye");
            togglePassword.classList.toggle("bi-eye-slash");
        });
    }

    /** ===== Form Validation ===== **/
    const loginForm = document.querySelector("#loginForm");
    if (loginForm) {
        loginForm.addEventListener("submit", function (event) {
            const username = document.querySelector("#username");
            const password = document.querySelector("#password");

            if (username.value.trim() === "" || password.value.trim() === "") {
                event.preventDefault(); // Stop form submission
                showErrorPopup("Please fill in both Username and Password.");
            }
        });
    }

    /** ===== Error Popup Function ===== **/
    function showErrorPopup(message) {
        let popup = document.createElement("div");
        popup.innerText = message;
        popup.style.position = "fixed";
        popup.style.top = "20px";
        popup.style.left = "50%";
        popup.style.transform = "translateX(-50%)";
        popup.style.backgroundColor = "#ff4d4d";
        popup.style.color = "white";
        popup.style.padding = "12px 20px";
        popup.style.borderRadius = "5px";
        popup.style.zIndex = "9999";
        popup.style.boxShadow = "0 2px 6px rgba(0,0,0,0.3)";
        popup.style.fontWeight = "bold";

        document.body.appendChild(popup);

        setTimeout(() => {
            popup.remove();
        }, 2500);
    }
});
