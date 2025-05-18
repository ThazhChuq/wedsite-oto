function initNavbar() {
    const hamburger = document.querySelector('.hamburger');
    const sidebar = document.querySelector('.sidebar');
    const sidebarClose = document.querySelector('.sidebar-close');
    const profileButton = document.querySelector('.profile');
    const loginModal = document.getElementById('login-modal');
    const modalClose = loginModal ? loginModal.querySelector('.modal-close') : null;
    const modalLoginBtn = loginModal ? loginModal.querySelector('#modal-login-btn') : null;
    const modalRegisterBtn = loginModal ? loginModal.querySelector('#modal-register-btn') : null;
    const modalCancelBtn = loginModal ? loginModal.querySelector('#modal-cancel-btn') : null;

    if (hamburger && sidebar && sidebarClose) {
        hamburger.addEventListener('click', () => {
            sidebar.classList.add('open');
            sidebar.setAttribute('aria-hidden', 'false');
        });

        sidebarClose.addEventListener('click', () => {
            sidebar.classList.remove('open');
            sidebar.setAttribute('aria-hidden', 'true');
        });

        // Optional: close sidebar when clicking outside
        document.addEventListener('click', (event) => {
            if (!sidebar.contains(event.target) && !hamburger.contains(event.target)) {
                if (sidebar.classList.contains('open')) {
                    sidebar.classList.remove('open');
                    sidebar.setAttribute('aria-hidden', 'true');
                }
            }
        });
    }

    // Show modal on profile button click
    if (profileButton && loginModal) {
        profileButton.addEventListener('click', () => {
            loginModal.classList.remove('hidden');
        });
    }

    // Show modal on "Hồ sơ" link click in sidebar
    const sidebarProfileLinks = Array.from(document.querySelectorAll('.sidebar-nav ul li a'));
    sidebarProfileLinks.forEach(link => {
        if (link.textContent.trim() === 'Hồ sơ') {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                if (loginModal) {
                    loginModal.classList.remove('hidden');
                }
            });
        }
    });

    // Close modal on close button click
    if (modalClose && loginModal) {
        modalClose.addEventListener('click', () => {
            loginModal.classList.add('hidden');
        });
    }

    // Close modal on clicking "Hủy" button
    if (modalCancelBtn && loginModal) {
        modalCancelBtn.addEventListener('click', () => {
            loginModal.classList.add('hidden');
        });
    }

    // Redirect on "Đăng nhập" button click
    if (modalLoginBtn) {
        modalLoginBtn.addEventListener('click', () => {
            window.location.href = 'pages/login.php';
        });
    }

    // Redirect on "Đăng ký" button click
    if (modalRegisterBtn) {
        modalRegisterBtn.addEventListener('click', () => {
            window.location.href = 'pages/register.php';
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Do nothing here, initNavbar will be called after navbar is loaded
});
