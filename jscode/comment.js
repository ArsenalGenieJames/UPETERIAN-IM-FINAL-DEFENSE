document.addEventListener("DOMContentLoaded", function () {
    const commentButtons = document.querySelectorAll('.show-comments');

    commentButtons.forEach(button => {
        button.addEventListener('click', function () {
            const postId = this.getAttribute('data-post-id');
            const commentsDiv = document.getElementById('comments-' + postId);
            const icon = this.querySelector('i'); // Get the Font Awesome icon inside the button

            if (commentsDiv.style.display === 'none' || commentsDiv.style.display === '') {
                commentsDiv.style.display = 'block';
                icon.classList.remove('fa-comment');
                icon.classList.add('fa-comment-dots'); // Toggle to "Hide Comments" icon
            } else {
                commentsDiv.style.display = 'none';
                icon.classList.remove('fa-comment-dots');
                icon.classList.add('fa-comment'); // Toggle back to "Show Comments" icon
            }
        });
    });
});
