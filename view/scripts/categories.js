// categories.js - Functionality for categories and topics pages

document.addEventListener("DOMContentLoaded", () => {
    // Category search functionality
    const categorySearchInput = document.querySelector('input[placeholder="Search categories..."]');
    if (categorySearchInput) {
      categorySearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const categoryCards = document.querySelectorAll('.card');
        
        categoryCards.forEach(card => {
          const categoryName = card.querySelector('h3').textContent.toLowerCase();
          if (categoryName.includes(searchTerm)) {
            card.style.display = 'block';
          } else {
            card.style.display = 'none';
          }
        });
      });
    }
  
    // Filter functionality for category detail page
    const filterForm = document.querySelector('.card form');
    if (filterForm) {
      filterForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // In a real application, you would collect all filter values and send them to the server
        // For this demo, we'll just show a loading state and then reset
        
        const submitButton = this.querySelector('button[type="submit"]');
        const originalText = submitButton.textContent;
        
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Applying...';
        
        // Simulate loading
        setTimeout(() => {
          submitButton.disabled = false;
          submitButton.textContent = originalText;
          
          // Show a success message
          const alertDiv = document.createElement('div');
          alertDiv.className = 'alert alert-success mt-3';
          alertDiv.textContent = 'Filters applied successfully!';
          filterForm.appendChild(alertDiv);
          
          // Remove the alert after 3 seconds
          setTimeout(() => {
            alertDiv.remove();
          }, 3000);
        }, 1000);
      });
    }
  
    // Topic tag click tracking
    const topicTags = document.querySelectorAll('[href^="/view/pages/topic.php"]');
    topicTags.forEach(tag => {
      tag.addEventListener('click', function(e) {
        const topicName = this.textContent.trim();
        console.log(`Topic clicked: ${topicName}`);
        // In a real app, you might want to track this with analytics
      });
    });
  });