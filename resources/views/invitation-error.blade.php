<!DOCTYPE html>
<html lang="ar" dir="rtl">
  <head>
    <meta charset="utf-8" />
    <title>{{__('admin.project-name')}} - الدعوة غير موجودة</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />
    <meta
      content="{{__('admin.project-name')}} - الدعوة غير موجودة"
      name="{{__('admin.project-name')}} - الدعوة غير موجودة"
    />
    <link
      rel="shortcut icon"
      href="{{asset('admin_assets/images/logo.png')}}"
    />
    <link rel="icon" href="{{asset('admin_assets/images/logo.png')}}" />

    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.0/TweenMax.min.js"></script>

    <style>
      @import "https://fonts.googleapis.com/css?family=Karla|Slackey|Sriracha|Cairo:300,400,600,700";

      * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        outline: none;
      }

      body {
        background: linear-gradient(
          135deg,
          #121223 0%,
          #1a1a3a 50%,
          #2d2d5f 100%
        );
        font-family: "Cairo", "Karla", sans-serif;
        min-height: 100vh;
        overflow-x: hidden;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        perspective: 1000px;
        position: relative;
      }

      body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: radial-gradient(
            circle at 30% 40%,
            rgba(255, 255, 255, 0.05) 0%,
            transparent 50%
          ),
          radial-gradient(
            circle at 70% 60%,
            rgba(255, 255, 255, 0.03) 0%,
            transparent 50%
          );
        z-index: -1;
        pointer-events: none;
      }

      .container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        overflow: visible;
        position: relative;
      }

      /* Glass effect */
      .glass {
        background: rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(18, 18, 35, 0.6),
          inset 0 1px 0 rgba(255, 255, 255, 0.1);
      }

      /* Error page content */
      .error-content {
        max-width: 600px;
        width: 100%;
        text-align: center;
        padding: 60px 40px;
        animation: slideUp 0.8s ease-out;
      }

      @keyframes slideUp {
        from {
          opacity: 0;
          transform: translateY(30px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      /* Invitation Icon */
      .error-icon {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 40px;
        font-size: 4em;
        font-weight: 700;
        background: linear-gradient(
          135deg,
          rgba(129, 140, 248, 0.2),
          rgba(129, 140, 248, 0.1)
        );
        color: #818cf8;
        border: 2px solid rgba(129, 140, 248, 0.3);
        box-shadow: 0 8px 32px rgba(129, 140, 248, 0.3);
        transition: all 0.3s ease;
      }

      .error-icon:hover {
        transform: scale(1.05);
        box-shadow: 0 12px 40px rgba(129, 140, 248, 0.4);
      }

      /* Error titles */
      .error-title {
        font-size: 4em;
        font-weight: 700;
        color: white;
        margin-bottom: 20px;
        text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
        line-height: 1.1;
      }

      .error-subtitle {
        font-size: 1.8em;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 15px;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
      }

      .error-description {
        color: rgba(255, 255, 255, 0.8);
        font-size: 1.2em;
        line-height: 1.6;
        margin-bottom: 50px;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
      }

      /* Action buttons */
      .error-actions {
        display: flex;
        gap: 20px;
        justify-content: center;
        margin-top: 40px;
      }

      .btn {
        padding: 18px 40px;
        border: none;
        border-radius: 12px;
        font-size: 1.2em;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-decoration: none;
        min-width: 150px;
      }

      .btn-primary {
        background: linear-gradient(135deg, #4ade80, #22c55e);
        color: white;
        box-shadow: 0 4px 15px rgba(34, 197, 94, 0.4);
      }

      .btn-primary:hover {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(34, 197, 94, 0.5);
      }

      .btn-secondary {
        background: transparent;
        color: rgba(255, 255, 255, 0.8);
        border: 2px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 15px rgba(255, 255, 255, 0.1);
      }

      .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(255, 255, 255, 0.2);
      }

      /* Floating elements animation */
      .floating-elements {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
      }

      .floating-element {
        position: absolute;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        animation: float 6s ease-in-out infinite;
      }

      .floating-element:nth-child(1) {
        top: 20%;
        left: 10%;
        animation-delay: 0s;
      }

      .floating-element:nth-child(2) {
        top: 60%;
        left: 80%;
        animation-delay: 1s;
      }

      .floating-element:nth-child(3) {
        top: 80%;
        left: 20%;
        animation-delay: 2s;
      }

      .floating-element:nth-child(4) {
        top: 30%;
        left: 70%;
        animation-delay: 3s;
      }

      @keyframes float {
        0%, 100% {
          transform: translateY(0) rotate(0deg);
          opacity: 0.3;
        }
        50% {
          transform: translateY(-20px) rotate(180deg);
          opacity: 0.6;
        }
      }

      /* Responsive Design */
      @media screen and (max-width: 768px) {
        .container {
          padding: 15px;
        }
        
        .error-content {
          padding: 40px 25px;
        }
        
        .error-icon {
          width: 100px;
          height: 100px;
          font-size: 3em;
          margin-bottom: 30px;
        }
        
        .error-title {
          font-size: 2.8em;
          margin-bottom: 15px;
        }
        
        .error-subtitle {
          font-size: 1.4em;
          margin-bottom: 12px;
        }
        
        .error-description {
          font-size: 1.1em;
          margin-bottom: 40px;
        }
        
        .error-actions {
          flex-direction: column;
          gap: 15px;
        }
        
        .btn {
          padding: 16px 30px;
          font-size: 1.1em;
        }
      }
      
      @media screen and (max-width: 480px) {
        .error-content {
          padding: 30px 20px;
        }
        
        .error-icon {
          width: 80px;
          height: 80px;
          font-size: 2.5em;
          margin-bottom: 25px;
        }
        
        .error-title {
          font-size: 2.2em;
          margin-bottom: 12px;
        }
        
        .error-subtitle {
          font-size: 1.2em;
          margin-bottom: 10px;
        }
        
        .error-description {
          font-size: 1em;
          margin-bottom: 30px;
        }
        
        .btn {
          padding: 14px 25px;
          font-size: 1em;
        }
      }
      
      @media screen and (max-width: 360px) {
        .error-content {
          padding: 25px 15px;
        }
        
        .error-icon {
          width: 70px;
          height: 70px;
          font-size: 2em;
          margin-bottom: 20px;
        }
        
        .error-title {
          font-size: 1.8em;
          margin-bottom: 10px;
        }
        
        .error-subtitle {
          font-size: 1.1em;
          margin-bottom: 8px;
        }
        
        .error-description {
          font-size: 0.95em;
          margin-bottom: 25px;
        }
        
        .btn {
          padding: 12px 20px;
          font-size: 0.95em;
        }
      }

      /* Landscape orientation adjustments */
      @media screen and (max-height: 550px) and (orientation: landscape) {
        .container {
          padding: 10px;
        }
        
        .error-content {
          padding: 20px;
        }
        
        .error-icon {
          width: 60px;
          height: 60px;
          font-size: 2em;
          margin-bottom: 15px;
        }
        
        .error-title {
          font-size: 2em;
          margin-bottom: 8px;
        }
        
        .error-subtitle {
          font-size: 1.1em;
          margin-bottom: 6px;
        }
        
        .error-description {
          font-size: 0.9em;
          margin-bottom: 20px;
        }
        
        .error-actions {
          flex-direction: row;
          gap: 15px;
        }
        
        .btn {
          padding: 10px 20px;
          font-size: 0.9em;
        }
      }

      /* Touch feedback for mobile */
      .btn:active {
        transform: scale(0.98);
      }

      /* Prevent text selection on mobile */
      .error-content {
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }
    </style>
  </head>

  <body>
    <div class="container">
      <!-- Floating background elements -->
      <div class="floating-elements">
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
      </div>

      <!-- Error content -->
      <div class="error-content glass">
        <div class="error-icon">
          📨
        </div>
        
        <h1 class="error-title">عذراً!</h1>
        <h2 class="error-subtitle">الدعوة غير موجودة</h2>
        <p class="error-description">
          {{ $message ?? 'الدعوة التي تبحث عنها غير موجودة أو قد تم حذفها. يرجى التحقق من رابط الدعوة أو التواصل مع الشخص الذي أرسل لك الدعوة.' }}
        </p>

        {{-- <div class="error-actions">
          <a href="{{ url('/') }}" class="btn btn-primary">
            🏠 العودة للرئيسية
          </a>
          <a href="javascript:history.back()" class="btn btn-secondary">
            ← الصفحة السابقة
          </a>
        </div> --}}
      </div>
    </div>

    <script>
      // Add touch support for mobile devices
      document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.btn');
        
        buttons.forEach(button => {
          // Add touch feedback
          button.addEventListener('touchstart', function(e) {
            this.style.transform = (this.style.transform || '') + ' scale(0.95)';
          }, { passive: true });
          
          button.addEventListener('touchend', function(e) {
            this.style.transform = this.style.transform.replace(' scale(0.95)', '');
          }, { passive: true });
          
          button.addEventListener('touchcancel', function(e) {
            this.style.transform = this.style.transform.replace(' scale(0.95)', '');
          }, { passive: true });
        });
      });

      // Prevent zoom on double tap for iOS
      let lastTouchEnd = 0;
      document.addEventListener('touchend', function (event) {
        const now = (new Date()).getTime();
        if (now - lastTouchEnd <= 300) {
          event.preventDefault();
        }
        lastTouchEnd = now;
      }, false);

      // Add subtle animation to floating elements
      document.addEventListener('DOMContentLoaded', function() {
        const floatingElements = document.querySelectorAll('.floating-element');
        
        floatingElements.forEach((element, index) => {
          // Add random positioning variation
          const randomX = Math.random() * 100;
          const randomY = Math.random() * 100;
          element.style.left = randomX + '%';
          element.style.top = randomY + '%';
          
          // Add random animation duration
          const randomDuration = 4 + Math.random() * 4; // 4-8 seconds
          element.style.animationDuration = randomDuration + 's';
        });
      });
    </script>
  </body>
</html> 
