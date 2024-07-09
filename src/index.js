document.addEventListener('DOMContentLoaded', function() {
    const loginModal = document.getElementById('loginModal');
    const registerModal = document.getElementById('registerModal');
    const forgetModal = document.getElementById('forgetModal');
    const loginButton = document.querySelector('.signup-button');
    const closeButtons = document.querySelectorAll('.close-btn');
    const backButton = document.querySelector('.back-btn');
    const registerLink = document.getElementById('registerLink');
    const loginLink = document.getElementById('loginLink');
    const forgetLinks = document.querySelectorAll('.forget');
    const filterToggle = document.querySelector('.filter-toggle');
    const filterBar = document.querySelector('.filter-bar');
    

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;

        // Set styles to make sure it shows up
        notification.style.position = 'fixed';
        notification.style.top = '20px';
        notification.style.left = '50%';
        notification.style.transform = 'translateX(-50%)';
        notification.style.backgroundColor = type === 'success' ? '#4CAF50' : '#f44336';
        notification.style.color = '#fff';
        notification.style.padding = '10px 20px';
        notification.style.width = '15rem';
        notification.style.borderRadius = '5px';
        notification.style.zIndex = '9999';
        notification.style.opacity = '1';
        notification.style.transition = 'opacity 0.3s ease';

        // Create progress bar
        const progressBar = document.createElement('div');
        progressBar.style.position = 'absolute';
        progressBar.style.bottom = '0';
        progressBar.style.left = '0';
        progressBar.style.width = '100%';
        progressBar.style.height = '2px';
        progressBar.style.backgroundColor = '#fff';
        progressBar.style.transition = 'width 1.5s linear';

        notification.appendChild(progressBar);

        document.body.appendChild(notification);

        setTimeout(() => {
            progressBar.style.width = '0';
        }, 0);

        setTimeout(function() {
            notification.style.opacity = '0';
            setTimeout(function() {
                document.body.removeChild(notification);
            }, 300);
        }, 1500);
    }

    const storedMessage = localStorage.getItem('notificationMessage');
    const storedType = localStorage.getItem('notificationType');
    if (storedMessage && storedType) {
        showNotification(storedMessage, storedType);
        localStorage.removeItem('notificationMessage');
        localStorage.removeItem('notificationType');
    }

    filterBar.style.display = 'none';
    filterToggle.addEventListener('click', () => {
        filterBar.style.display = filterBar.style.display === 'none' ? 'block' : 'none';
    });

    if (loginButton) {
        loginButton.addEventListener('click', function() {
            loginModal.style.display = 'flex';  // Show login modal
        });
    }

    closeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            loginModal.style.display = 'none';
            registerModal.style.display = 'none';
            forgetModal.style.display = 'none';
        });
    });

    if (backButton) {
        backButton.addEventListener('click', function() {
            forgetModal.style.display = 'none';
            loginModal.style.display = 'flex';  // Show login modal
        });
    }

    window.addEventListener('click', function(event) {
        if (event.target === loginModal) {
            loginModal.style.display = 'none';
        }
        if (event.target === registerModal) {
            registerModal.style.display = 'none';
        }
        if (event.target === forgetModal) {
            forgetModal.style.display = 'none';
        }
    });

    if (registerLink) {
        registerLink.addEventListener('click', function(event) {
            event.preventDefault();
            registerModal.style.display = 'flex';
            loginModal.style.display = 'none';
        });
    }

    if (loginLink) {
        loginLink.addEventListener('click', function(event) {
            event.preventDefault();
            registerModal.style.display = 'none';
            loginModal.style.display = 'flex';  // Show login modal
        });
    }

    if (forgetLinks) {
        forgetLinks.forEach(link => {
            link.addEventListener('click', function(event) {
                event.preventDefault();
                loginModal.style.display = 'none';
                forgetModal.style.display = 'flex';  // Show forget modal
            });
        });
    }

    document.getElementById('loginForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);

        fetch('login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                localStorage.setItem('notificationMessage', 'Login Successfully!');
                localStorage.setItem('notificationType', 'success');
                location.reload();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => console.error('Error:', error));
    });

    document.getElementById('registerForm').addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(this);

        fetch('register.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                loginModal.style.display = 'flex';
                forgetModal.style.display = 'none';
                registerModal.style.display = 'none';
                localStorage.setItem('notificationMessage', 'Registered Successfully! You can now login');
                localStorage.setItem('notificationType', 'success');
                location.reload();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => console.error('Error:', error));
    });

    document.getElementById('forgetForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(this);

        fetch('forget.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                loginModal.style.display = 'flex';
                forgetModal.style.display = 'none';
                localStorage.setItem('notificationMessage', 'Password reset successfully! You can now login.');
                localStorage.setItem('notificationType', 'success');
                location.reload();
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => console.error('Error:', error));
    });
    
    
});
