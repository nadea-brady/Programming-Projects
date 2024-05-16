document.addEventListener("DOMContentLoaded", function() {
    // Get all course cards
    var courseCards = document.querySelectorAll('.new-student-card');

    // Loop through each course card
    courseCards.forEach(function(card) {
        // Add click event listener to each card
        card.addEventListener("click", function() {
            // Extract course name and course description from the card
            var courseName = card.querySelector('.card--footer .title').textContent;
            
            // Redirect to the notes page with course name as a query parameter
            window.location.href = `notes.php?courseName=${encodeURIComponent(courseName)}`;
        });
    });
});
