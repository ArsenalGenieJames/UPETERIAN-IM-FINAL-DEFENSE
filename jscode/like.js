// Select the heart icon and like count elements
const heartIcon = document.querySelector('.heart');
const likeCount = document.querySelector('.like-count');

// Check localStorage for saved reaction state
const isReacted = localStorage.getItem('isReacted');

// Apply the saved state on page load
if (isReacted === 'true') {
    heartIcon.classList.remove('fa-regular');
    heartIcon.classList.add('fa-solid');
    likeCount.style.display = 'block'; // Show the like count if already reacted
}

// Add a click event listener
heartIcon.addEventListener('click', () => {
    if (heartIcon.classList.contains('fa-regular')) {
        // Change to solid heart, save state, and show like count
        heartIcon.classList.remove('fa-regular');
        heartIcon.classList.add('fa-solid');
        likeCount.style.display = 'block'; // Show like count
        localStorage.setItem('isReacted', 'true');
    } else {
        // Change back to regular heart, clear state, and keep like count visible
        heartIcon.classList.remove('fa-solid');
        heartIcon.classList.add('fa-regular');
        localStorage.setItem('isReacted', 'false');
    }
});
