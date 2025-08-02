<?php
// Set language based on cookie
$language = 'en';
if (isset($_COOKIE['lang']) && in_array($_COOKIE['lang'], ['en', 'ru'])) {
    $language = $_COOKIE['lang'];
}

// Translations
$translations = [
    'en' => [
        'title' => 'Key Verification',
        'placeholder' => 'enter key',
        'continue' => 'Continue',
        'checking' => 'Verifying key...',
        'valid' => 'Correct key.',
        'invalid' => 'Invalid key.',
        'error' => 'Verification failed. Please try again.',
        'switch_lang' => 'Русский'
    ],
    'ru' => [
        'title' => 'Проверка ключа',
        'placeholder' => 'введите ключ',
        'continue' => 'Продолжить',
        'checking' => 'Проверка ключа...',
        'valid' => 'Ключ верный.',
        'invalid' => 'Неверный ключ.',
        'error' => 'Ошибка проверки. Попробуйте снова.',
        'switch_lang' => 'English'
    ]
];

$t = $translations[$language];
?>
<!DOCTYPE html>
<html lang="<?php echo $language; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $t['title']; ?></title>
    <style>
        :root {
            --fluent-accent: #0078d7;
            --fluent-dark: #000000;
            --fluent-card: #1a1a1a;
            --fluent-text: #ffffff;
            --fluent-text-secondary: #a0a0a0;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: var(--fluent-dark);
            color: var(--fluent-text);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .container {
            background-color: var(--fluent-card);
            border-radius: 8px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        
        h1 {
            margin-top: 0;
            font-weight: 600;
            font-size: 24px;
        }
        
        .input-group {
            display: flex;
            margin: 20px 0;
            position: relative;
        }
        
        input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid #333;
            border-radius: 4px 0 0 4px;
            background-color: #2a2a2a;
            color: var(--fluent-text);
            font-size: 16px;
            outline: none;
            transition: border-color 0.2s;
            position: relative;
            z-index: 1;
        }
        
        input:focus {
            border-color: var(--fluent-accent);
        }
        
        input::placeholder {
            color: var(--fluent-text-secondary);
        }
        
        button {
            padding: 12px 20px;
            background-color: var(--fluent-accent);
            color: white;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: background-color 0.2s;
            position: relative;
            z-index: 1;
        }
        
        button:hover {
            background-color: #106ebe;
        }
        
        button:disabled {
            background-color: #505050;
            cursor: not-allowed;
        }
        
        /* Gradient animation */
        .input-group::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(90deg, transparent, rgba(0, 120, 215, 0.5), transparent);
            background-size: 200% 100%;
            border-radius: 6px;
            z-index: 0;
            opacity: 0;
            transition: opacity 0.3s;
            animation: gradientFlow 2s linear infinite;
        }
        
        .input-group:focus-within::before {
            opacity: 1;
        }
        
        @keyframes gradientFlow {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }
        
        /* Notification styles */
        .notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: var(--fluent-card);
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            max-width: 300px;
            transform: translateY(100px);
            opacity: 0;
            transition: transform 0.3s, opacity 0.3s;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }
        
        .notification.show {
            transform: translateY(0);
            opacity: 1;
        }
        
        .notification-title {
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .progress-bar {
            height: 4px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
            margin-top: 8px;
        }
        
        .progress {
            height: 100%;
            background-color: var(--fluent-accent);
            width: 0%;
            transition: width 0.1s linear;
        }
        
        .language-switch {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            color: var(--fluent-text-secondary);
            cursor: pointer;
            font-size: 14px;
        }
        
        .language-switch:hover {
            color: var(--fluent-text);
        }
    </style>
</head>
<body>
    <button class="language-switch" id="languageSwitch"><?php echo $t['switch_lang']; ?></button>
    
    <div class="container">
        <h1><?php echo $t['title']; ?></h1>
        <div class="input-group">
            <input type="text" id="keyInput" placeholder="<?php echo $t['placeholder']; ?>">
            <button id="continueButton"><?php echo $t['continue']; ?></button>
        </div>
    </div>
    
    <div class="notification" id="notification">
        <div class="notification-title" id="notificationTitle"><?php echo $t['checking']; ?></div>
        <div class="progress-bar">
            <div class="progress" id="progressBar"></div>
        </div>
    </div>
    
    <script>
        // Language switching
        document.getElementById('languageSwitch').addEventListener('click', function() {
            const newLang = '<?php echo $language === 'en' ? 'ru' : 'en'; ?>';
            document.cookie = `lang=${newLang}; path=/; max-age=31536000`; // 1 year
            location.reload();
        });
        
        // Key verification
        document.getElementById('continueButton').addEventListener('click', verifyKey);
        document.getElementById('keyInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') verifyKey();
        });
        
        function verifyKey() {
            const key = document.getElementById('keyInput').value.trim();
            if (!key) return;
            
            const button = document.getElementById('continueButton');
            button.disabled = true;
            
            // Show notification
            const notification = document.getElementById('notification');
            const notificationTitle = document.getElementById('notificationTitle');
            const progressBar = document.getElementById('progressBar');
            
            notificationTitle.textContent = '<?php echo $t['checking']; ?>';
            notification.classList.add('show');
            
            // Simulate progress (real API call will happen in parallel)
            let progress = 0;
            const progressInterval = setInterval(() => {
                progress += 1;
                progressBar.style.width = `${progress}%`;
                
                if (progress >= 100) {
                    clearInterval(progressInterval);
                }
            }, 20);
            
            // Real API call
            fetch('https://api.nyawon.tech/api.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-API-KEY': 'api_gZzm5FDWaEdZ9rFbucZ8Cec0' // Must match API key from api.php
                },
                body: JSON.stringify({ key: key })
            })
            .then(response => response.json())
            .then(data => {
                clearInterval(progressInterval);
                progressBar.style.width = '100%';
                
                setTimeout(() => {
                    if (data.valid) {
                        notificationTitle.textContent = '<?php echo $t['valid']; ?>';
                    } else {
                        notificationTitle.textContent = '<?php echo $t['invalid']; ?>';
                    }
                    
                    // Hide after 3 seconds
                    setTimeout(() => {
                        notification.classList.remove('show');
                        button.disabled = false;
                    }, 3000);
                }, 100);
            })
            .catch(error => {
                clearInterval(progressInterval);
                progressBar.style.width = '100%';
                
                setTimeout(() => {
                    notificationTitle.textContent = '<?php echo $t['error']; ?>';
                    
                    setTimeout(() => {
                        notification.classList.remove('show');
                        button.disabled = false;
                    }, 3000);
                }, 100);
            });
        }
    </script>
</body>
</html>
