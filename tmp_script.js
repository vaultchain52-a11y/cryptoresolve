
        async function handleLogin(event) {
            event.preventDefault();
            const username = document.getElementById('adminUsername').value.trim();
            const password = document.getElementById('adminPassword').value.trim();
            const errorEl = document.getElementById('loginError');
            errorEl.classList.add('hidden');

            const response = await fetch('/api/admin.php?action=login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ username, password })
            });

            const result = await response.json();
            if (result.success) {
                window.location.href = 'hidden-admin.html';
                return true;
            }

            errorEl.innerText = result.error || 'Access denied. Invalid credentials.';
            errorEl.classList.remove('hidden');
            return false;
        }
    