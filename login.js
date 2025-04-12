document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch('login.php', {
        method: 'POST',
        body: new FormData(this)
    })
    .then(response => {
        if (response.redirected) {
            window.location.href = response.url;
            return;
        }
        return response.json();
    })
    .then(data => {
        if (data && data.success === false) {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
});