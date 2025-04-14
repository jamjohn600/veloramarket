document.addEventListener("DOMContentLoaded", () => {
    const signupForm = document.getElementById("signupForm");
    const signupBtn = document.getElementById("signupBtn");
    const password = document.getElementById("floatingPassword");
    const confirmPassword = document.getElementById("floatingPassword2");
    password.addEventListener("input", () => {
        const value = password.value;
        let strength = "";
        if (
            value.length >= 8 &&
            /[A-Z]/.test(value) &&
            /[a-z]/.test(value) &&
            /\d/.test(value) &&
            /[\W_]/.test(value)
        ) {
            strength = "Strong";
        } else {
            strength = "Weak (8+ chars, upper, lower, number, special)";
        }
        Swal.fire({
            icon: "info",
            title: "Password Strength",
            text: strength,
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 2000,
        });
    });
    signupForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        signupBtn.disabled = true;
        signupBtn.innerHTML = "<span>Creating account...</span>";
        if (password.value !== confirmPassword.value) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Passwords do not match.",
            });
            signupBtn.disabled = false;
            signupBtn.innerHTML = "<span>Signup Now</span>";
            return;
        }
        const formData = new FormData(signupForm);
        try {
            const response = await fetch("model/account.php", {
                method: "POST",
                body: formData,
            });
            const result = await response.json();
            if (result.success) {
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: result.message,
                    timer: 1500,
                    showConfirmButton: false,
                }).then(() => {
                    window.location.href = result.redirect;
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: result.message,
                });
            }
        } catch (error) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: "Something went wrong. Please try again.",
            });
        } finally {
            signupBtn.disabled = false;
            signupBtn.innerHTML = "<span>Signup Now</span>";
        }
    });
});
