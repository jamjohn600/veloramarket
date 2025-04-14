$('#emailVerificationButton').on('click', function () {
    $.ajax({
        url: '../../model/send_email_verification.php',
        method: 'POST',
        success: function (response) {
            try {
                const res = JSON.parse(response);

                if (res.status === 'success') {
                    // Code was newly generated and email was sent
                    if (confirm('Verification Code Sent Successfully! Click OK to proceed.')) {
                        showModal();
                    }
                } else if (res.status === 'exists') {
                    // Code is still valid
                    if (confirm('Your verification code is still valid. Click OK to proceed.')) {
                        showModal();
                    }
                } else {
                    // Some error occurred
                    alert(res.message);
                }
            } catch (e) {
                console.error('Error parsing JSON:', e);
                alert('An error occurred while processing your request. Please try again.');
            }
        },
        error: function () {
            alert('Error in sending email. Please try again later.');
        }
    });
});

// Helper function to show the modal and manage the overlay issue
function showModal() {
    $('#emailModal').modal('show');

    // Handle modal close to prevent overlay issue
    $('#emailModal').on('hidden.bs.modal', function () {
        // Remove any lingering modal-backdrop
        $('.modal-backdrop').remove();
        // Ensure body is scrollable again
        $('body').removeClass('modal-open');
        $('body').css('padding-right', '');
    });
}


document.getElementById('btc').addEventListener('click',function(){
    $('#depositModal').modal('hide')
    $('#deposit_bitcoin').modal('show')
})

document.getElementById('transferBtn').addEventListener('click',function(){
    console.log('')
    $('#depositModal').modal('hide')
    $('#deposit_bitcoin').modal('hide')
     $('#deposit_trans').modal('show')
})


$('#emailVerificationForm').on('submit', function (e) {
    e.preventDefault();
    const code = $('#emailVerificationCode').val();
    
    $.ajax({
        url: '../../model/verify_email_code.php',
        method: 'POST',
        data: { code: code },
        success: function (response) {
            const res = JSON.parse(response);
            if (res.status === 'success') {
                alert('Email verified successfully!');
                location.reload(); // Reload the page to reflect changes
            } else {
                alert(res.message);
            }
        },
        error: function () {
            alert('Error in verifying the code.');
        }
    });
});


$('#updateProfileForm').submit(function(e) {
    e.preventDefault();

    // Create form data
    var formData = new FormData(this);

    $.ajax({
        url: '../../model/update_profile.php',
        type: 'POST',
        data: formData,
        processData: false,  // Prevent jQuery from converting the data into a query string
        contentType: false,  // Let jQuery set the content type
        dataType: 'json',    // Expect a JSON response
        success: function(response) {
            if (response.success) {
                alert("Profile updated successfully!");
                window.location.reload();  // Reload the page after success
            } else {
                alert(response.error || "An unknown error occurred.");
            }
        },
        error: function(xhr, status, error) {
            alert("An error occurred: " + error);
        }
    });
});

$('#securityUpdateForm').on('submit', function(e) {
    e.preventDefault();

    // Basic client-side validation
    const currentPass = $('#currentPass').val().trim();
    const newPass = $('#newPass').val().trim();
    const securityOne = $('#securityOne').val();
    const securityAnswer = $('#securityAnswer').val().trim();

    if (!currentPass || !newPass || !securityOne || !securityAnswer) {
        alert('Please fill in all required fields.');
        return;
    }

    $.ajax({
        url: '../../model/update_security.php',
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json', // Ensure the response is parsed as JSON
        success: function(response) {
            if (response.success) {
                alert('Security information updated successfully!');
                location.reload(); // Reload the page after successful update
            } else {
                alert(response.error); // Show the error message
            }
        },
        error: function() {
            alert('An error occurred. Please try again.');
        }
    });
});

$(document).ready(function() {
    $('#depositBtn').on('click', function() {
        // AJAX call to check eligibility
        $.ajax({
            url: '../../model/check_eligibility.php', // Backend script
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.eligible) {
                    // Show deposit modal
                    $('#depositModal').modal('show');
                    alert('You are eligible to make a deposit.');
                } else {
                    // Show error alert
                    alert(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: ", status, error);
                alert('An error occurred while verifying eligibility. Please try again.');
            }
        });
    });
});


// $(document).ready(function(){
//     $('#depositBtn').click(function(){
//         // Perform AJAX request to check verification status
//         $.ajax({
//             url: '../../model/check_verification_status.php', // PHP file to check user verification
//             type: 'POST',
//             dataType: 'json',
//             success: function(response){
//                 if(response.email_verified == 1 && response.transaction_status == 'ready'){
//                     // Open modal if checks pass
//                     $('#depositModal').modal('show');
//                 } else {
//                     // Output failure message and redirect
//                     if(response.email_verified == 0){
//                         alert("Please verify your email before making a deposit.");
//                     } else if(response.transaction_status != 'ready'){
//                         alert("You cannot make a deposit at this time.");
//                     }
//                     window.location.href = '../../client/profile';
//                 }
//             },
//             error: function(){
//                 alert("Something went wrong. Please try again.");
//             }
//         });
//     });
// });


// $(document).ready(function(){
//     $('#depositForm').submit(function(e){
//         e.preventDefault();

//         // Get form data
//         let depositAmount = $('#depositAmount').val();
//         let username = $('#username').val();
//         let email = $('#email').val();
//         let paymentType = $('#paymentType').val();

//         // Make AJAX request to initiate the Flutterwave payment
//         $.ajax({
//             url: '../../model/initiate_payment.php', // Backend script to handle payment initiation
//             type: 'POST',
//             data: {
//                 deposit_amount: depositAmount,
//                 username: username,
//                 email: email,
//                 payment_type: paymentType
//             },
//             dataType: 'json',
//             success: function(response){
//                 if(response.status == 'success'){
//                     // Redirect the user to Flutterwave payment link
//                     window.location.href = response.payment_link;
//                 } else {
//                     alert(response.message); // Show error message
//                 }
//             },
//             error: function(){
//                 alert("Something went wrong. Please try again.");
//             }
//         });
//     });
// });