$(document).ready(function () {
    // Attach a click event listener to all buttons with the "blink-indicator" class
    $(".blink-indicator").on("click", function () {
		console.log('yessss')
        const customerId = $(this).data("customer-id");
        const customerName = $(this).data("customer-name");
        const modalElement = $("#documentRequestModal");
        const requestList = modalElement.find("#documentRequestList");

        // Set the customer name in the modal
        modalElement.find("#modalCustomerName").text(customerName);

        // Fetch document requests via AJAX
        $.ajax({
            url: `/document-requests/${customerId}`,
            method: "GET",
            success: function (data) {
                // Clear previous list
                requestList.empty();

                // Populate the list with new data
                if (data.length === 0) {
                    requestList.html("<li>No requests found for this customer.</li>");
                } else {
                    data.forEach(function (request) {
                        const listItem = `<li>${request.document_type}: ${request.document_details}</li>`;
                        requestList.append(listItem);
                    });
                }
            },
            error: function () {
                console.error("Error fetching document requests.");
                requestList.html("<li>Error loading requests.</li>");
            },
        });

        // Show the modal
        const bootstrapModal = new bootstrap.Modal(document.getElementById("documentRequestModal"));
        bootstrapModal.show();
    });
});

