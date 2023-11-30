<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Markets Team Data Tools</title>
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
  <header>
    <img src="{{ asset('images/Logo - Quadrado.png') }}" alt="Datagro Logo">
    <h2>Markets Team Data Tools</h2>
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
          <div>
              <button type="submit" class="button">Login</button>
          </div>
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

  <footer>
    <img src="{{ asset('images/Logo - Banner Médio - Markets - 2.png') }}" alt="Datagro Markets Logo">
    <div>
      <h2>DATAGRO LINKS</h2>
      <ul>
          <li><a href="https://www.datagro.com/en/" target="_blank">www.datagro.com</a></li>
          <li><a href="https://portal.datagro.com/" target="_blank">portal.datagro.com</a></li>
          <li><a href="https://www.linkedin.com/company/datagro" target="_blank">Datagro LinkedIn</a></li>
      </ul>
    </div>
  </footer>

  <script>console.log('[login.blade.php] Login view loaded');</script>
</body>
</html>
