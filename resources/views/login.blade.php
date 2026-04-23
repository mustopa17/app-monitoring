<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - App Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at top left, #1e293b, #0f172a);
        }
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .input-glass {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: white;
        }
        .input-glass:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
            outline: none;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full glass p-8 rounded-2xl shadow-2xl animate-fade-in">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Welcome Back</h1>
            <p class="text-slate-400">Sign in to your monitoring dashboard</p>
        </div>

        <form id="loginForm" class="space-y-6">
            <div>
                <label for="email" class="block text-sm font-medium text-slate-300 mb-1">Email Address</label>
                <input type="email" id="email" name="email" required 
                    class="w-full px-4 py-3 rounded-lg input-glass transition-all duration-200" 
                    placeholder="name@company.com">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-slate-300 mb-1">Password</label>
                <input type="password" id="password" name="password" required 
                    class="w-full px-4 py-3 rounded-lg input-glass transition-all duration-200" 
                    placeholder="••••••••">
            </div>

            <div id="errorMessage" class="hidden p-3 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 text-sm text-center">
            </div>

            <button type="submit" id="submitBtn"
                class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg transition-all duration-200 transform hover:scale-[1.02] active:scale-[0.98]">
                Sign In
            </button>
        </form>

        <div class="mt-8 text-center">
            <p class="text-slate-500 text-xs uppercase tracking-widest font-bold">App Monitoring v1.0</p>
        </div>
    </div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const errorDiv = document.getElementById('errorMessage');
            const submitBtn = document.getElementById('submitBtn');
            
            // UI State: Loading
            errorDiv.classList.add('hidden');
            submitBtn.disabled = true;
            submitBtn.innerText = 'Signing in...';
            submitBtn.classList.add('opacity-70', 'cursor-not-allowed');

            try {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (response.ok) {
                    // Success: Store token and redirect
                    localStorage.setItem('auth_token', data.token);
                    window.location.href = '/';
                } else {

                    // Error from server
                    errorDiv.innerText = data.error || 'Login failed. Please check your credentials.';
                    errorDiv.classList.remove('hidden');
                }
            } catch (err) {
                // Network error
                errorDiv.innerText = 'Unable to connect to the server. Please try again later.';
                errorDiv.classList.remove('hidden');
            } finally {
                // Restore UI State
                submitBtn.disabled = false;
                submitBtn.innerText = 'Sign In';
                submitBtn.classList.remove('opacity-70', 'cursor-not-allowed');
            }
        });
    </script>
</body>
</html>
