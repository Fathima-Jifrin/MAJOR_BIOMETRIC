<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Casted Successfully</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            font-family: 'Poppins', sans-serif;
            margin: 0;
        }
        .container {
            text-align: center;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }
        .icon {
            font-size: 60px;
            color: #27ae60;
            margin-bottom: 20px;
        }
        h1 {
            color: #333;
            font-size: 24px;
        }
        p {
            color: #555;
            font-size: 16px;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 25px;
            background: #27ae60;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            transition: 0.3s;
            cursor: pointer;
            border: none;
        }
        .button:hover {
            background: #219150;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">✔</div>
        <h1>Vote Casted Successfully</h1>
        <p>Your vote has been recorded securely.</p>
        <button class="button" onclick="logoutAndRedirect()">Finish</button>
    </div>

    <script>
        function logoutAndRedirect() {
            fetch('../logout.php')
                .then(response => response.text())
                .then(() => {
                    
                    window.location.href = '../log.html';
                })
                .catch(error => console.error('Logout failed:', error));
        }
    </script>
</body>
</html>
