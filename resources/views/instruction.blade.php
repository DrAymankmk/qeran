<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        img {
            width: 150px;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
        }
        p {
            color: #555;
            margin: 20px 0;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
<div class="container">
    <!-- Replace the src with the correct logo URL -->
    <img src="{{ asset('admin_assets/images/new-logo.jpeg') }}" alt="App Logo">

    <h1>قران</h1>
    <div dir="rtl">
        <p>
            نحن آسفون لرؤيتك تغادر، لكننا نتفهم إذا كنت ترغب في إغلاق حسابك. بدلاً من ذلك، يمكنك وضع حسابك في استراحة لاستخدامه لاحقًا. إذا كنت تصر على إغلاق حسابك، فإليك كيفية القيام بذلك:
        </p>
        <p>
            الخطوة 1: قم بتسجيل الدخول إلى حسابك.
        </p>
        <p>
            الخطوة 2: انقر على خيار "الاعدادات".
        </p>
        <p>
            الخطوة 3: اختر "حذف حسابي".
        </p>
        <p>
            ملاحظة: بمجرد تقديم طلبك، لن تتمكن من استعادة حسابك.
        </p>
    </div>

</div>
</body>
</html>