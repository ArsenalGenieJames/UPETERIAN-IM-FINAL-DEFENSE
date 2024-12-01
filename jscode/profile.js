document.querySelector('#profile_picture').addEventListener('change', function () {
    const formData = new FormData();
    formData.append('profile_picture', this.files[0]);

    fetch('profile.php', {
        method: 'POST',
        body: formData,
    })
        .then(response => response.json()) // Ensure the response is parsed as JSON
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Update the profile picture dynamically
                document.querySelector('#profile-img').src = data.profile_picture;
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the profile picture.');
        });
});