<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Markets Team Data Tools</title>
  <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>
<body class="login-page">
  <header class="login-header">
    <img src="{{ asset('images/Logo - Quadrado.png') }}" alt="Datagro Logo" style="height: 50px;">
    <h2>DATAGRO Markets - Data Preview Platform</h2>
  </header>

  <main class="login-container">
    <div class="login-box">
      <form method="POST" action="{{ route('login') }}" class="login-form">
          @csrf
          <div class="input-group">
              <label for="email">Login:</label>
              <input type="text" name="email" id="email" required autofocus>
          </div>
          <div class="input-group">
              <label for="password">Password:</label>
              <input type="password" name="password" id="password" required>
          </div>
          <button type="submit" class="button login-button">Login</button>
          @if ($errors->any())
              <div class="error-messages">
                  <ul>
                      @foreach ($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
              </div>
          @endif
      </form>
    </div>
  </main>

  <footer class="login-footer">
    <!-- You can keep the image if it is part of the design -->
    <img src="{{ asset('images/Logo - Banner Médio - Markets - 2.png') }}" alt="Datagro Markets Logo" style="height: 50px;">
  </footer>

  <script>console.log('[login.blade.php] Login view loaded');</script>
</body>
</html>
