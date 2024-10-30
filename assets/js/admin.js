// admin.js

jQuery(document).ready(function($) {
    // Add New Job button click
    $('#servicehub-add-job-button').click(function(e) {
      e.preventDefault();
  
      // Create a modal overlay
      var overlay = $('<div id="servicehub-modal-overlay"></div>');
      overlay.appendTo('body');
  
      // Clone the form and append it to the overlay
      var form = $('#servicehub-add-job-form').clone();
      form.appendTo(overlay);
  
      // Add a close button to the form
      var closeButton = $('<button type="button" class="servicehub-modal-close">Close</button>');
      closeButton.appendTo(form);
  
      // Show the overlay
      overlay.fadeIn();
  
      // Close button click
      closeButton.click(function() {
        overlay.fadeOut(function() {
          $(this).remove();
        });
      });
    });
  
    // Form submission (example with AJAX)
    $(document).on('submit', '#add-job-form', function(e) { // Use event delegation
      e.preventDefault();
  
      // Get form data
      var formData = $(this).serialize();
  
      // Send AJAX request
      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
          action: 'servicehub_add_job',
          formData: formData,
          nonce: servicehub_vars.nonce,
        },
        success: function(response) {
          // Handle success (e.g., display success message, update job list)
          console.log(response);
  
          // Close the modal
          $('#servicehub-modal-overlay').fadeOut(function() {
            $(this).remove();
          });
        },
        error: function(error) {
          // Handle error (e.g., display error message)
          console.error(error);
        }
      });
    });
  
    // Add New Customer button click
    $('#servicehub-add-customer-button').click(function(e) {
      e.preventDefault();
  
      // Create a modal overlay
      var overlay = $('<div id="servicehub-modal-overlay"></div>');
      overlay.appendTo('body');
  
      // Clone the form and append it to the overlay
      var form = $('#servicehub-add-customer-form').clone();
      form.appendTo(overlay);
  
      // Add a close button to the form
      var closeButton = $('<button type="button" class="servicehub-modal-close">Close</button>');
      closeButton.appendTo(form);
  
      // Show the overlay
      overlay.fadeIn();
  
      // Close button click
      closeButton.click(function() {
        overlay.fadeOut(function() {
          $(this).remove();
        });
      });
    });
  
    // Customer Form submission (example with AJAX)
    $(document).on('submit', '#add-customer-form', function(e) { // Use event delegation
      e.preventDefault();
  
      // Get form data
      var formData = $(this).serialize();
  
      // Send AJAX request
      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
          action: 'servicehub_add_customer', // New AJAX action for adding customer
          formData: formData,
          nonce: servicehub_vars.customer_nonce, // Use separate nonce for customer
        },
        success: function(response) {
          // Handle success (e.g., display success message, update customer list)
          console.log(response);
  
          // Close the modal
          $('#servicehub-modal-overlay').fadeOut(function() {
            $(this).remove();
          });
        },
        error: function(error) {
          // Handle error (e.g., display error message)
          console.error(error);
        }
      });
    });
  
    // Add New Invoice button click
    $('#servicehub-add-invoice-button').click(function(e) {
      e.preventDefault();
  
      // Create a modal overlay
      var overlay = $('<div id="servicehub-modal-overlay"></div>');
      overlay.appendTo('body');
  
      // Clone the form and append it to the overlay
      var form = $('#servicehub-add-invoice-form').clone();
      form.appendTo(overlay);
  
      // Add a close button to the form
      var closeButton = $('<button type="button" class="servicehub-modal-close">Close</button>');
      closeButton.appendTo(form);
  
      // Show the overlay
      overlay.fadeIn();
  
      // Close button click
      closeButton.click(function() {
        overlay.fadeOut(function() {
          $(this).remove();
        });
      });
    });
  
    // Invoice Form submission (example with AJAX)
    $(document).on('submit', '#add-invoice-form', function(e) { // Use event delegation
      e.preventDefault();
  
      // Get form data
      var formData = $(this).serialize();
  
      // Send AJAX request
      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
          action: 'servicehub_add_invoice', // New AJAX action for adding invoice
          formData: formData,
          nonce: servicehub_vars.invoice_nonce, // Use separate nonce for invoice
        },
        success: function(response) {
          // Handle success (e.g., display success message, update invoice list)
          console.log(response);
  
          // Close the modal
          $('#servicehub-modal-overlay').fadeOut(function() {
            $(this).remove();
          });
        },
        error: function(error) {
          // Handle error (e.g., display error message)
          console.error(error);
        }
      });
    });
  
    // Edit Job link click
    $(document).on('click', '.servicehub-edit-job', function(e) {
      e.preventDefault();
  
      var jobId = $(this).data('job-id');
  
      // Fetch job data via AJAX
      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
          action: 'servicehub_get_job_data',
          job_id: jobId,
          nonce: servicehub_vars.nonce, // You might need a separate nonce for this
        },
        success: function(response) {
          if (response.success) {
            var job = response.data;
  
            // Populate the edit form with job data
            $('#edit_job_id').val(job.id);
            $('#edit_job_title').val(job.title);
            $('#edit_job_description').val(job.description);
            $('#edit_job_customer').val(job.customer_id);
            $('#edit_job_status').val(job.status);
            $('#edit_job_technician').val(job.assigned_technician);
            $('#edit_job_scheduled_date').val(job.scheduled_date);
  
            // Create a modal overlay
            var overlay = $('<div id="servicehub-modal-overlay"></div>');
            overlay.appendTo('body');
  
            // Clone the edit form and append it to the overlay
            var form = $('#servicehub-edit-job-form').clone();
            form.appendTo(overlay);
  
            // Add a close button to the form
            var closeButton = $('<button type="button" class="servicehub-modal-close">Close</button>');
            closeButton.appendTo(form);
  
            // Show the overlay
            overlay.fadeIn();
  
            // Close button click
            closeButton.click(function() {
              overlay.fadeOut(function() {
                $(this).remove();
              });
            });
          } else {
            // Handle error (e.g., display error message)
            console.error(response.data.message);
          }
        },
        error: function(error) {
          // Handle error (e.g., display error message)
          console.error(error);
        }
      });
    });
  
    // Edit Job form submission
    $(document).on('submit', '#edit-job-form', function(e) {
      e.preventDefault();
  
      // Get form data
      var formData = $(this).serialize();
  
      // Send AJAX request
      $.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
          action: 'servicehub_update_job',
          formData: formData,
          nonce: servicehub_vars.nonce, // You might need a separate nonce for this
        },
        success: function(response) {
          // Handle success (e.g., display success message, update job list)
          console.log(response);
  
          // Close the modal
          $('#servicehub-modal-overlay').fadeOut(function() {
            $(this).remove();
          });
        },
        error: function(error) {// Handle error (e.g., display error message)
            console.error(error);
          }
        });
      });
    
      // Delete Job link click
      $(document).on('click', '.servicehub-delete-job', function(e) {
        e.preventDefault();
    
        var jobId = $(this).data('job-id');
    
        // Confirmation dialog (you can customize this)
        if (confirm('Are you sure you want to delete this job?')) {
          // Send AJAX request
          $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
              action: 'servicehub_delete_job',
              job_id: jobId,
              nonce: servicehub_vars.nonce, // You might need a separate nonce for this
            },
            success: function(response) {
              // Handle success (e.g., display success message, remove job from list)
              console.log(response);
            },
            error: function(error) {
              // Handle error (e.g., display error message)
              console.error(error);
            }
          });
        }
      });
    
      // Edit Customer link click
      $(document).on('click', '.servicehub-edit-customer', function(e) {
        e.preventDefault();
    
        var customerId = $(this).data('customer-id');
    
        // Fetch customer data via AJAX
        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'servicehub_get_customer_data',
            customer_id: customerId,
            nonce: servicehub_vars.customer_nonce, // Use customer nonce here
          },
          success: function(response) {
            if (response.success) {
              var customer = response.data;
    
              // Populate the edit form with customer data
              $('#edit_customer_id').val(customer.id);
              $('#edit_customer_name').val(customer.name);
              $('#edit_customer_email').val(customer.email);
              $('#edit_customer_phone').val(customer.phone);
              $('#edit_customer_address').val(customer.address);
    
              // Create a modal overlay
              var overlay = $('<div id="servicehub-modal-overlay"></div>');
              overlay.appendTo('body');
    
              // Clone the edit form and append it to the overlay
              var form = $('#servicehub-edit-customer-form').clone();
              form.appendTo(overlay);
    
              // Add a close button to the form
              var closeButton = $('<button type="button" class="servicehub-modal-close">Close</button>');
              closeButton.appendTo(form);
    
              // Show the overlay
              overlay.fadeIn();
    
              // Close button click
              closeButton.click(function() {
                overlay.fadeOut(function() {
                  $(this).remove();
                });
              });
            } else {
              // Handle error (e.g., display error message)
              console.error(response.data.message);
            }
          },
          error: function(error) {
            // Handle error (e.g., display error message)
            console.error(error);
          }
        });
      });
    
      // Edit Customer form submission
      $(document).on('submit', '#edit-customer-form', function(e) {
        e.preventDefault();
    
        // Get form data
        var formData = $(this).serialize();
    
        // Send AJAX request
        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'servicehub_update_customer',
            formData: formData,
            nonce: servicehub_vars.customer_nonce, // Use customer nonce here
          },
          success: function(response) {
            // Handle success (e.g., display success message, update customer list)
            console.log(response);
    
            // Close the modal
            $('#servicehub-modal-overlay').fadeOut(function() {
              $(this).remove();
            });
          },
          error: function(error) {
            // Handle error (e.g., display error message)
            console.error(error);
          }
        });
      });
    
      // Delete Customer link click
      $(document).on('click', '.servicehub-delete-customer', function(e) {
        e.preventDefault();
    
        var customerId = $(this).data('customer-id');
    
        // Confirmation dialog (you can customize this)
        if (confirm('Are you sure you want to delete this customer?')) {
          // Send AJAX request
          $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
              action: 'servicehub_delete_customer',
              customer_id: customerId,
              nonce: servicehub_vars.customer_nonce, // Use customer nonce here
            },
            success: function(response) {
              // Handle success (e.g., display success message, remove customer from list)
              console.log(response);
            },
            error: function(error) {
              // Handle error (e.g., display error message)
              console.error(error);
            }
          });
        }
      });
    
      // Edit Invoice link click
      $(document).on('click', '.servicehub-edit-invoice', function(e) {
        e.preventDefault();
    
        var invoiceId = $(this).data('invoice-id');
    
        // Fetch invoice data via AJAX
        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'servicehub_get_invoice_data',
            invoice_id: invoiceId,
            nonce: servicehub_vars.invoice_nonce, // Use invoice nonce here
          },
          success: function(response) {
            if (response.success) {
              var invoice = response.data;
    
              // Populate the edit form with invoice data
              $('#edit_invoice_id').val(invoice.id);
              $('#edit_invoice_job').val(invoice.job_id);
              $('#edit_invoice_customer').val(invoice.customer_id);
              $('#edit_invoice_amount').val(invoice.amount);
              $('#edit_invoice_status').val(invoice.status);
              $('#edit_invoice_due_date').val(invoice.due_date);
    
              // Create a modal overlay
              var overlay = $('<div id="servicehub-modal-overlay"></div>');
              overlay.appendTo('body');
    
              // Clone the edit form and append it to the overlay
              var form = $('#servicehub-edit-invoice-form').clone();
              form.appendTo(overlay);
    
              // Add a close button to the form
              var closeButton = $('<button type="button" class="servicehub-modal-close">Close</button>');
              closeButton.appendTo(form);
    
              // Show the overlay
              overlay.fadeIn();
    
              // Close button click
              closeButton.click(function() {
                overlay.fadeOut(function() {
                  $(this).remove();
                });
              });
            } else {
              // Handle error (e.g., display error message)
              console.error(response.data.message);
            }
          },
          error: function(error) {
            // Handle error (e.g., display error message)
            console.error(error);
          }
        });
      });
    
      // Edit Invoice form submission
      $(document).on('submit', '#edit-invoice-form', function(e) {
        e.preventDefault();
    
        // Get form data
        var formData = $(this).serialize();
    
        // Send AJAX request
        $.ajax({
          url: ajaxurl,
          type: 'POST',
          data: {
            action: 'servicehub_update_invoice',
            formData: formData,
            nonce: servicehub_vars.invoice_nonce, // Use invoice nonce here
          },
          success: function(response) {
            // Handle success (e.g., display success message, update invoice list)
            console.log(response);
    
            // Close the modal
            $('#servicehub-modal-overlay').fadeOut(function() {
              $(this).remove();
            });
          },
          error: function(error) {
            // Handle error (e.g., display error message)
            console.error(error);
          }
        });
      });
    
      // Delete Invoice link click
      $(document).on('click', '.servicehub-delete-invoice', function(e) {
        e.preventDefault();
    
        var invoiceId = $(this).data('invoice-id');
    
        // Confirmation dialog (you can customize this)
        if (confirm('Are you sure you want to delete this invoice?')) {
          // Send AJAX request
          $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
              action: 'servicehub_delete_invoice',
          invoice_id: invoiceId,
          nonce: servicehub_vars.invoice_nonce, // Use invoice nonce here
        },
        success: function(response) {
          // Handle success (e.g., display success message, remove invoice from list)
          console.log(response);
        },
        error: function(error) {
          // Handle error (e.g., display error message)
          console.error(error);
        }
      });
    }
  });
});