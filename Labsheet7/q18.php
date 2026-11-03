<?php
require_once __DIR__ . '/db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q18 - Client-Side JavaScript Form Validation</title>
    <link rel="stylesheet" href="bookstore.css">
    <style>
        .validation-tabs {
            display: flex;
            gap: 12px;
            margin-bottom: 24px;
            border-bottom: 2px solid var(--border);
            padding-bottom: 12px;
        }

        .tab-btn {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid var(--border);
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .tab-btn.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .form-panel {
            display: none;
            background: white;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 32px;
            box-shadow: var(--shadow);
            max-width: 650px;
            margin: 0 auto;
        }

        .form-panel.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-group.has-error input {
            border-color: var(--danger);
            background: #fef2f2;
        }

        .form-group.has-success input {
            border-color: var(--success);
            background: #f0fdf4;
        }

        .error-hint {
            display: none;
            font-size: 12px;
            color: var(--danger);
            font-weight: 600;
            margin-top: 4px;
        }

        .form-group.has-error .error-hint {
            display: block;
        }
    </style>
</head>
<body>

    <div class="lab-header-banner">
        <span><strong>Lab Sheet 7 - Question 18:</strong> Client-side form validation in JavaScript for registration, login, and checkout forms</span>
        <a href="index.html">← Back to Lab Sheet 7 Index</a>
    </div>

    <nav class="bookstore-nav">
        <div class="nav-inner">
            <a href="q1.php" class="logo">Book<span>Vault</span></a>
            <ul class="nav-links">
                <li><a href="q1.php">Home</a></li>
                <li><a href="q18.php" class="active">Form Validation Suite</a></li>
                <li><a href="q6.php" class="cart-chip">🛒 Cart (<?php echo count($_SESSION['cart']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <header style="margin-bottom: 24px; text-align: center;">
            <h1 style="font-size: 28px; font-weight: 800; margin-bottom: 6px;">Client-Side Validation Suite</h1>
            <p style="color: var(--gray-muted);">Switch between tabs below to test JavaScript validation on Registration, Login, and Checkout forms.</p>
        </header>

        <!-- Tab Selectors -->
        <div class="validation-tabs" style="justify-content: center;">
            <button class="tab-btn active" onclick="switchTab('regPanel', this)">1. Registration Validation</button>
            <button class="tab-btn" onclick="switchTab('loginPanel', this)">2. Login Validation</button>
            <button class="tab-btn" onclick="switchTab('checkoutPanel', this)">3. Checkout Validation</button>
        </div>

        <div id="statusBanner" style="display: none; max-width: 650px; margin: 0 auto 20px;" class="alert alert-success"></div>

        <!-- Panel 1: Registration Form -->
        <div id="regPanel" class="form-panel active">
            <h2 style="font-size: 20px; margin-bottom: 16px;">Registration Form Validation</h2>
            <form id="regForm" novalidate>
                <div class="form-group" id="regNameGroup">
                    <label>Full Name *</label>
                    <input type="text" id="regName" class="form-control" placeholder="e.g. Rohan Sharma">
                    <span class="error-hint">Full name must be at least 3 letters.</span>
                </div>

                <div class="form-group" id="regEmailGroup">
                    <label>Email Address *</label>
                    <input type="email" id="regEmail" class="form-control" placeholder="user@domain.com">
                    <span class="error-hint">Please enter a valid email address (e.g. user@domain.com).</span>
                </div>

                <div class="form-group" id="regPassGroup">
                    <label>Password (min 6 characters) *</label>
                    <input type="password" id="regPass" class="form-control" placeholder="At least 6 characters">
                    <span class="error-hint">Password must be at least 6 characters long.</span>
                </div>

                <div class="form-group" id="regConfirmGroup">
                    <label>Confirm Password *</label>
                    <input type="password" id="regConfirm" class="form-control" placeholder="Re-type password">
                    <span class="error-hint">Passwords do not match!</span>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Validate &amp; Submit Registration</button>
            </form>
        </div>

        <!-- Panel 2: Login Form -->
        <div id="loginPanel" class="form-panel">
            <h2 style="font-size: 20px; margin-bottom: 16px;">Login Form Validation</h2>
            <form id="loginForm" novalidate>
                <div class="form-group" id="loginUserGroup">
                    <label>Username *</label>
                    <input type="text" id="loginUser" class="form-control" placeholder="Enter username">
                    <span class="error-hint">Username is required.</span>
                </div>

                <div class="form-group" id="loginPassGroup">
                    <label>Password *</label>
                    <input type="password" id="loginPass" class="form-control" placeholder="Enter password">
                    <span class="error-hint">Password is required.</span>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Validate &amp; Submit Login</button>
            </form>
        </div>

        <!-- Panel 3: Checkout Form -->
        <div id="checkoutPanel" class="form-panel">
            <h2 style="font-size: 20px; margin-bottom: 16px;">Checkout Form Validation</h2>
            <form id="checkoutForm" novalidate>
                <div class="form-group" id="chkNameGroup">
                    <label>Recipient Name *</label>
                    <input type="text" id="chkName" class="form-control" placeholder="Full name">
                    <span class="error-hint">Please enter a recipient name.</span>
                </div>

                <div class="form-group" id="chkPhoneGroup">
                    <label>Mobile Number (10 Digits) *</label>
                    <input type="text" id="chkPhone" class="form-control" placeholder="e.g. 9876543210" maxlength="10">
                    <span class="error-hint">Please enter a valid 10-digit mobile number.</span>
                </div>

                <div class="form-group" id="chkPinGroup">
                    <label>Postal PIN Code (6 Digits) *</label>
                    <input type="text" id="chkPin" class="form-control" placeholder="e.g. 247667" maxlength="6">
                    <span class="error-hint">Please enter a valid 6-digit PIN code.</span>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">Validate &amp; Proceed to Payment</button>
            </form>
        </div>
    </main>

    <footer class="bookstore-footer">
        &copy; 2026 COER University - Web Designing Lab Sheet 7 (Question 18: Client-Side JS Form Validation).
    </footer>

    <!-- JavaScript Client-Side Validation Logic -->
    <script>
        const statusBanner = document.getElementById('statusBanner');

        // Tab Switching
        function switchTab(panelId, btn) {
            document.querySelectorAll('.form-panel').forEach(p => p.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            document.getElementById(panelId).classList.add('active');
            btn.classList.add('active');
            statusBanner.style.display = 'none';
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const phoneRegex = /^[0-9]{10}$/;
        const pinRegex = /^[0-9]{6}$/;

        function setFieldState(groupElement, isValid) {
            if (isValid) {
                groupElement.classList.remove('has-error');
                groupElement.classList.add('has-success');
            } else {
                groupElement.classList.remove('has-success');
                groupElement.classList.add('has-error');
            }
            return isValid;
        }

        // 1. Registration Validation
        document.getElementById('regForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const nameVal = document.getElementById('regName').value.trim();
            const emailVal = document.getElementById('regEmail').value.trim();
            const passVal = document.getElementById('regPass').value;
            const confirmVal = document.getElementById('regConfirm').value;

            const isNameValid = setFieldState(document.getElementById('regNameGroup'), nameVal.length >= 3);
            const isEmailValid = setFieldState(document.getElementById('regEmailGroup'), emailRegex.test(emailVal));
            const isPassValid = setFieldState(document.getElementById('regPassGroup'), passVal.length >= 6);
            const isConfirmValid = setFieldState(document.getElementById('regConfirmGroup'), confirmVal === passVal && confirmVal.length >= 6);

            if (isNameValid && isEmailValid && isPassValid && isConfirmValid) {
                statusBanner.textContent = "✓ Client-Side Registration Validation Passed! Ready for secure server transmission.";
                statusBanner.style.display = 'block';
            } else {
                statusBanner.style.display = 'none';
            }
        });

        // 2. Login Validation
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const userVal = document.getElementById('loginUser').value.trim();
            const passVal = document.getElementById('loginPass').value;

            const isUserValid = setFieldState(document.getElementById('loginUserGroup'), userVal.length > 0);
            const isPassValid = setFieldState(document.getElementById('loginPassGroup'), passVal.length > 0);

            if (isUserValid && isPassValid) {
                statusBanner.textContent = "✓ Client-Side Login Validation Passed!";
                statusBanner.style.display = 'block';
            } else {
                statusBanner.style.display = 'none';
            }
        });

        // 3. Checkout Validation
        document.getElementById('checkoutForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const nameVal = document.getElementById('chkName').value.trim();
            const phoneVal = document.getElementById('chkPhone').value.trim();
            const pinVal = document.getElementById('chkPin').value.trim();

            const isNameValid = setFieldState(document.getElementById('chkNameGroup'), nameVal.length >= 3);
            const isPhoneValid = setFieldState(document.getElementById('chkPhoneGroup'), phoneRegex.test(phoneVal));
            const isPinValid = setFieldState(document.getElementById('chkPinGroup'), pinRegex.test(pinVal));

            if (isNameValid && isPhoneValid && isPinValid) {
                statusBanner.textContent = "✓ Client-Side Checkout Address Validation Passed!";
                statusBanner.style.display = 'block';
            } else {
                statusBanner.style.display = 'none';
            }
        });
    </script>

</body>
</html>
