<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>LGU2 — Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;500;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#115272',
            'primary-dark': '#0d3f55',
            'primary-light': '#1a6e96',
          }
        }
      }
    }
  </script>
  <style>
    body {
      font-family: "Montserrat", system-ui, -apple-system, Arial, sans-serif;
    }
    
    .card-shadow {
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
    
    .input-transition {
      transition: all 0.3s ease;
    }
    
    .gradient-bg {
      background: linear-gradient(135deg, #115272 0%, #0d3f55 100%);
    }
    
    .logo-text {
      text-shadow: 2px 4px 8px rgba(0,0,0,0.28);
    }
    
    .form-section {
      position: relative;
      overflow: hidden;
    }
    
    .form-section::before {
      content: "";
      position: absolute;
      width: 200%;
      height: 200%;
      top: -50%;
      left: -50%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
      transform: rotate(-5deg);
      z-index: 0;
    }
  </style>
</head>

<body class="min-h-screen flex items-center justify-center bg-cover bg-center bg-fixed p-4 bg-gray-100"
  style="background-image: url('data:image/svg+xml;utf8,<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"100\" height=\"100\" viewBox=\"0 0 100 100\"><rect width=\"100\" height=\"100\" fill=\"%23f0f4f8\"/><path d=\"M0 0L100 100\" stroke=\"%23dbeafe\" stroke-width=\"2\"/><path d=\"M100 0L0 100\" stroke=\"%23dbeafe\" stroke-width=\"2\"/></svg>')">

  <div class="flex w-full max-w-4xl h-auto md:h-[550px] rounded-xl card-shadow overflow-hidden bg-white">

    <!-- Left Branding -->
    <div class="hidden md:flex w-2/5 items-center justify-center gradient-bg p-8 relative">
      <div class="absolute top-6 left-6 flex items-center">
        <div class="w-3 h-3 rounded-full bg-white/30 mr-2"></div>
        <div class="w-3 h-3 rounded-full bg-white/30 mr-2"></div>
        <div class="w-3 h-3 rounded-full bg-white/30"></div>
      </div>
      
      <div class="text-center z-10">
        <div class="flex items-end gap-3 justify-center mb-4">
          <div class="text-6xl font-extrabold text-white logo-text">LGU</div>
          <div class="text-6xl font-extrabold text-white/90 logo-text">2</div>
        </div>
        <div class="text-sm font-bold tracking-widest text-white/80 mb-8">
          LOCAL GOVERNMENT UNIT
        </div>
        
        <div class="w-64 h-64 mx-auto mb-8 flex items-center justify-center">
          <div class="w-full h-full rounded-full border-4 border-white/20 flex items-center justify-center">
            <div class="w-11/12 h-11/12 rounded-full border-4 border-white/30 flex items-center justify-center">
              <div class="w-5/6 h-5/6 rounded-full border-4 border-white/40 flex items-center justify-center">
                <i class="fas fa-building text-white/30 text-5xl"></i>
              </div>
            </div>
          </div>
        </div>
        
        <p class="text-white/70 text-sm max-w-xs mx-auto">
          Secure access to government services and information
        </p>
      </div>
      
      <div class="absolute bottom-6 left-0 right-0 text-center text-white/50 text-xs">
        © 2023 LGU Portal System v2.1
      </div>
    </div>

    <!-- Right Login Form -->
    <div class="flex w-full md:w-3/5 items-center justify-center bg-white p-8 md:p-10 form-section">
      <div class="w-full max-w-sm z-10">
        <div class="text-center mb-8">
          <h2 class="text-2xl font-bold text-gray-800 mb-2">Welcome Back</h2>
          <p class="text-gray-600 text-sm">Sign in to continue to your account</p>
        </div>

        <!-- Display Error Messages -->
        @if ($errors->any())
          <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
            @foreach ($errors->all() as $error)
              <p>{{ $error }}</p>
            @endforeach
          </div>
        @endif

        @if (session('success'))
          <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4 text-sm">
            {{ session('success') }}
          </div>
        @endif

        <form method="POST" action="{{ route('authenticate') }}" class="space-y-5">
          @csrf
          
          <!-- Email -->
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-envelope text-gray-400"></i>
              </div>
              <input id="email" name="email" type="email" placeholder="Enter your email" required
                value="{{ old('email') }}"
                class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-light focus:border-transparent input-transition outline-none" />
            </div>
          </div>

          <!-- Password -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-lock text-gray-400"></i>
              </div>
              <input id="password" name="password" type="password" placeholder="Enter your password" required
                class="w-full pl-10 pr-10 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-primary-light focus:border-transparent input-transition outline-none" />
              <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" id="togglePassword">
                <i class="fas fa-eye text-gray-400 hover:text-gray-600"></i>
              </button>
            </div>
          </div>

          <!-- Remember & Forgot -->
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" />
              <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
            </div>
            <a href="#" class="text-sm text-primary hover:text-primary-dark font-medium">Forgot Password?</a>
          </div>

          <!-- Button -->
          <button type="submit"
            class="w-full py-3 px-4 rounded-lg gradient-bg text-white font-semibold shadow-md hover:shadow-lg transition-all duration-300 flex items-center justify-center">
            <span>Sign In</span>
            <i class="fas fa-arrow-right ml-2"></i>
          </button>
          
          <!-- Divider -->
          <div class="relative flex items-center my-6">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="flex-shrink mx-4 text-gray-500 text-sm">or continue with</span>
            <div class="flex-grow border-t border-gray-300"></div>
          </div>
          
          <!-- Social Login -->
          <div class="grid grid-cols-2 gap-3">
            <button type="button" class="py-2 px-4 bg-blue-50 text-blue-600 rounded-lg font-medium hover:bg-blue-100 transition flex items-center justify-center">
              <i class="fab fa-facebook-f mr-2"></i> Facebook
            </button>
            <button type="button" class="py-2 px-4 bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 transition flex items-center justify-center">
              <i class="fab fa-google mr-2"></i> Google
            </button>
          </div>
        </form>
        
        <div class="text-center mt-8 text-sm text-gray-600">
          Don't have an account? <a href="{{ route('register') }}" class="text-primary font-medium hover:text-primary-dark">Register Now</a>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      
      // Toggle eye icon
      this.querySelector('i').classList.toggle('fa-eye');
      this.querySelector('i').classList.toggle('fa-eye-slash');
    });
    
    // Add focus styles to inputs
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
      input.addEventListener('focus', () => {
        input.parentElement.classList.add('ring-2', 'ring-primary-light', 'rounded-lg');
      });
      
      input.addEventListener('blur', () => {
        input.parentElement.classList.remove('ring-2', 'ring-primary-light', 'rounded-lg');
      });
    });
  </script>
</body>

</html>