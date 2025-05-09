<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['success_message']) && !empty($_SESSION['success_message'])): 
?>
    <div id="success-box">
        <span class="close-btn" onclick="closeSuccess()">&times;</span>
        <p><?php echo htmlspecialchars($_SESSION['success_message']); ?></p>
    </div>

    <script>
        function closeSuccess() {
            document.getElementById('success-box').style.display = 'none';
        }
        // Auto-hide success message after 5 seconds
        setTimeout(closeSuccess, 5000);
    </script>

    <style>
        #success-box {
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(to right, #e8f5e9, #c8e6c9);
            padding: 15px 40px 15px 60px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            z-index: 1000;
            color: #388e3c;
            border-left: 4px solid #388e3c;
            font-size: 15px;
            animation: slideIn 0.3s ease-out forwards;
            transform-origin: top right;
            min-width: 300px;
            display: flex;
            align-items: center;
        }

        #success-box p {
            margin: 0;
            position: relative;
            font-weight: 500;
        }

        .close-btn {
            position: absolute;
            top: 8px;
            right: 12px;
            font-size: 20px;
            cursor: pointer;
            color: #1b5e20;
            transition: color 0.2s;
        }

        .close-btn:hover {
            color: #004d00;
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
    </style>
<?php 
    unset($_SESSION['success_message']); // Unset success message after displaying it
endif; 
?>
