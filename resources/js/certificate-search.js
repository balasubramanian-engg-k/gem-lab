// public/js/certificate-search.js

$(document).ready(function(){
    $("#searchBtn").click(function(){
        let certNo = $("#certificate_no").val().trim();
        let messageDiv = $("#message");
        let iframe = $("#certificateFrame");

        // Clear old messages and frame
        messageDiv.html('');
        iframe.hide().attr('src', '');

        // Validation
        if(certNo === ''){
            messageDiv.html('<div class="alert alert-danger">Please enter a certificate number.</div>');
            return;
        }

        // AJAX request
        $.ajax({
            url: CERTIFICATE_SEARCH_URL+'/'+certNo, // from Blade variable
            type: "GET",
            data: { certificate_no: certNo },
            beforeSend: function(){
                messageDiv.html('<div class="alert alert-info">Searching...</div>');
            },
            success: function(response){
                if(response.status === 'success'){
                    messageDiv.html('<div class="alert alert-success">Certificate found!</div>');
                    iframe.attr('src', CERTIFICATE_URL+'/'+certNo).show();
                } else {
                    messageDiv.html('<div class="alert alert-warning">No certificate found for this number.</div>');
                }
            },
            error: function(){
                messageDiv.html('<div class="alert alert-danger">An error occurred while searching. Please try again.</div>');
            }
        });
    });
});
