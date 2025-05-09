<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['error_message']) && !empty($_SESSION['error_message'])): 
?>
    <div id="error-box">
        <span class="close-btn" onclick="closeError()">&times;</span>
        <p><?php echo htmlspecialchars($_SESSION['error_message']); ?></p>
    </div>

    <script>
        function closeError() {
            const errorBox = document.getElementById('error-box');
            errorBox.style.animation = 'slideOut 0.3s ease-out forwards';
            setTimeout(() => errorBox.style.display = 'none', 300);
        }

        // Auto-hide error after 5 seconds with animation
        setTimeout(closeError, 5000);
    </script>

    <style>
        #error-box {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(to right, #ffebee, #ffcdd2);
            padding: 15px 40px 15px 60px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            z-index: 1000;
            color: #d32f2f;
            border-left: 4px solid #d32f2f;
            font-size: 15px;
            animation: slideIn 0.3s ease-out forwards;
            transform-origin: top right;
            min-width: 300px;
            max-width: 90vw;
            display: flex;
            align-items: center;
            transition: transform 0.3s, opacity 0.3s;
        }

        #error-box p {
            margin: 0;
            position: relative;
            font-weight: 500;
        }

        #error-box p::before {
            content: "!";
            position: absolute;
            left: -35px;
            top: 50%;
            transform: translateY(-50%);
            width: 24px;
            height: 24px;
            background-color: #d32f2f;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
        }

        .close-btn {
            position: absolute;
            top: 8px;
            right: 12px;
            font-size: 20px;
            cursor: pointer;
            color: #b71c1c;
            transition: color 0.2s;
            line-height: 1;
        }

        .close-btn:hover {
            color: #8b0000;
        }

        @keyframes slideIn {
            from {
                transform: translateX(120%) scale(0.9);
                opacity: 0;
            }
            to {
                transform: translateX(0) scale(1);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0) scale(1);
                opacity: 1;
            }
            to {
                transform: translateX(120%) scale(0.9);
                opacity: 0;
            }
        }
    </style>
<?php 
    unset($_SESSION['error_message']); // Unset error after displaying it
endif; 
?>
