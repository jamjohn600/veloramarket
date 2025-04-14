document.addEventListener("DOMContentLoaded", () => {
    const signinForm = document.getElementById("signinForm");
    const signinBtn = document.getElementById("signinBtn");
    if (!signinForm || !signinBtn) {
        return;
    }
    signinForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        signinBtn.disabled = true;
        signinBtn.innerHTML = "<span>Logging in...</span>";
        const formData = new FormData(signinForm);
        try {
            const response = await fetch("model/login.php", {
                method: "POST",
                body: formData,
            });
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const result = await response.json();
            if (result.success) {
                Swal.fire({
                    icon: "success",
                    title: "Success",
                    text: result.message,
                    timer: 1500,
                    showConfirmButton: false,
                }).then(() => {
                    window.location.href = result.redirect || "dashboard.php";
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
            signinBtn.disabled = false;
            signinBtn.innerHTML = "<span>Sign In</span>";
        }
    });
});