<?php
// Create data directory if it doesn't exist
$dataDir = __DIR__ . '/data';
$uploadsDir = __DIR__ . '/uploads';

if (!is_dir($dataDir)) {
    mkdir($dataDir, 0755, true);
}

if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0755, true);
}

// Get all saved forms
$savedForms = [];
if (is_dir($dataDir)) {
    $files = glob($dataDir . '/*.json');
    foreach ($files as $file) {
        $savedForms[] = json_decode(file_get_contents($file), true);
    }
    // Sort by timestamp in descending order
    usort($savedForms, function($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });
}

$showFilled = isset($_GET['show']) && $_GET['show'] === 'filled';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Form Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@300;400;500&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', 'Roboto', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            min-height: 100vh;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
            z-index: -1;
        }

        body::after {
            content: '';
            position: fixed;
            bottom: -20%;
            left: -5%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite reverse;
            z-index: -1;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) translateX(0); }
            50% { transform: translateY(30px) translateX(20px); }
        }

        .container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(15px);
            border-radius: 25px;
            box-shadow: 0 25px 70px rgba(0, 0, 0, 0.15), 0 0 50px rgba(102, 126, 234, 0.15);
            padding: 50px;
            width: 100%;
            max-width: 900px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: slideUp 0.6s ease-out;
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

        .header {
            text-align: center;
            margin-bottom: 40px;
            animation: fadeIn 0.8s ease-out;
        }

        .header-icon {
            font-size: 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
        }

        h1 {
            color: #4c2a7f;
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
            font-family: 'Poppins', sans-serif;
        }

        .subtitle {
            color: #7c6ba8;
            font-size: 15px;
            font-weight: 500;
            font-family: 'Roboto', sans-serif;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .form-group {
            margin-bottom: 25px;
            animation: slideUp 0.6s ease-out forwards;
            opacity: 0;
        }

        .form-group:nth-child(1) { animation-delay: 0.1s; }
        .form-group:nth-child(2) { animation-delay: 0.15s; }
        .form-group:nth-child(3) { animation-delay: 0.2s; }
        .form-group:nth-child(4) { animation-delay: 0.25s; }
        .form-group:nth-child(5) { animation-delay: 0.3s; }
        .form-group:nth-child(6) { animation-delay: 0.35s; }
        .form-group:nth-child(7) { animation-delay: 0.4s; }
        .form-group:nth-child(8) { animation-delay: 0.45s; }
        .form-group:nth-child(9) { animation-delay: 0.5s; }
        .form-group:nth-child(10) { animation-delay: 0.55s; }
        .form-group:nth-child(11) { animation-delay: 0.6s; }

        label {
            display: block;
            margin-bottom: 10px;
            color: #4c2a7f;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-family: 'Poppins', sans-serif;
        }

        .input-wrapper {
            position: relative;
        }

        input[type="text"],
        input[type="email"],
        input[type="date"],
        textarea,
        input[type="file"] {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e6e0f8;
            border-radius: 12px;
            font-family: 'Roboto', sans-serif;
            font-size: 14px;
            background: #f7f5fc;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        input[type="text"]::placeholder,
        input[type="email"]::placeholder,
        textarea::placeholder {
            color: #b3a1d4;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="date"]:focus,
        textarea:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.12);
        }

        input[type="file"] {
            padding: 10px;
            cursor: pointer;
        }

        input[type="file"]::-webkit-file-upload-button {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            cursor: pointer;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.3s;
            margin-right: 10px;
            font-family: 'Poppins', sans-serif;
        }

        input[type="file"]::-webkit-file-upload-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(26, 188, 156, 0.4);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
            font-family: 'Roboto', sans-serif;
        }

        .radio-group,
        .checkbox-group {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .radio-item,
        .checkbox-item {
            display: flex;
            align-items: center;
            position: relative;
        }

        input[type="radio"],
        input[type="checkbox"] {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
            accent-color: #764ba2;
            transition: all 0.3s;
        }

        input[type="radio"]:hover,
        input[type="checkbox"]:hover {
            transform: scale(1.1);
        }

        .radio-item label,
        .checkbox-item label {
            margin: 0;
            font-weight: 500;
            color: #4c2a7f;
            cursor: pointer;
            font-size: 14px;
            font-family: 'Roboto', sans-serif;
        }

        .file-preview {
            margin-top: 10px;
            padding: 10px;
            background: #f3f0fb;
            border-radius: 8px;
            font-size: 12px;
            color: #4c2a7f;
            font-family: 'Roboto', sans-serif;
        }

        .button-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 40px;
        }

        button {
            padding: 16px 28px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
            font-family: 'Poppins', sans-serif;
        }

        button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        button:active::before {
            width: 300px;
            height: 300px;
        }

        .btn-save {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-display {
            background: white;
            color: #764ba2;
            border: 2px solid #764ba2;
            box-shadow: 0 4px 15px rgba(118, 75, 162, 0.2);
        }

        .btn-display:hover {
            background: rgba(118, 75, 162, 0.08);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(118, 75, 162, 0.3);
        }

        .btn-back {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            width: 100%;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-back:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
        }

        /* Filled Forms Styles */
        .filled-forms-container {
            display: none;
        }

        .filled-forms-container.show {
            display: block;
        }

        .form-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .form-card {
            border: none;
            border-radius: 15px;
            padding: 25px;
            background: linear-gradient(135deg, #FFFFFF 0%, #f7f5fc 100%);
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.08);
            position: relative;
            overflow: hidden;
        }

        .form-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            transition: left 0.4s ease;
        }

        .form-card:hover::before {
            left: 100%;
        }

        .form-card:hover {
            box-shadow: 0 15px 40px rgba(102, 126, 234, 0.25);
            transform: translateY(-8px);
        }

        .card-photo {
            width: 80px;
            height: 80px;
            border-radius: 12px;
            margin-bottom: 15px;
            object-fit: cover;
            border: 3px solid #667eea;
        }

        .form-card h3 {
            color: #4c2a7f;
            margin-bottom: 12px;
            font-size: 18px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
        }

        .form-card p {
            color: #7c6ba8;
            font-size: 13px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            font-family: 'Roboto', sans-serif;
        }

        .form-card p i {
            margin-right: 8px;
            color: #764ba2;
            width: 16px;
        }

        .form-details {
            background: linear-gradient(135deg, #FFFFFF 0%, #f7f5fc 100%);
            border-radius: 15px;
            padding: 40px;
            margin-bottom: 30px;
            border: none;
            box-shadow: 0 10px 40px rgba(102, 126, 234, 0.12);
            animation: slideUp 0.6s ease-out;
        }

        .form-details-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #e6e0f8;
        }

        .detail-photo {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            object-fit: cover;
            margin-right: 25px;
            border: 4px solid #667eea;
        }

        .detail-header-text h2 {
            color: #4c2a7f;
            margin-bottom: 8px;
            font-size: 26px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
        }

        .detail-header-text p {
            color: #7c6ba8;
            font-size: 14px;
            font-family: 'Roboto', sans-serif;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .detail-item {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .detail-label {
            color: #667eea;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            font-family: 'Poppins', sans-serif;
        }

        .detail-label i {
            margin-right: 8px;
            font-size: 14px;
        }

        .detail-value {
            color: #4c2a7f;
            font-size: 15px;
            font-weight: 500;
            word-break: break-word;
            font-family: 'Roboto', sans-serif;
        }

        .detail-item-full {
            grid-column: 1 / -1;
        }

        .resume-link {
            display: inline-block;
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            padding: 8px 12px;
            background: rgba(102, 126, 234, 0.12);
            border-radius: 6px;
            transition: all 0.3s;
            font-family: 'Roboto', sans-serif;
        }

        .resume-link:hover {
            background: rgba(102, 126, 234, 0.25);
            color: #764ba2;
        }

        .empty-message {
            text-align: center;
            padding: 60px 40px;
            color: #b3a1d4;
            font-size: 18px;
            animation: fadeIn 0.6s ease-out;
            font-family: 'Roboto', sans-serif;
        }

        .empty-icon {
            font-size: 60px;
            color: #e6e0f8;
            margin-bottom: 20px;
        }

        .success-message {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            animation: slideDown 0.5s ease-out;
            font-family: 'Poppins', sans-serif;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 25px;
            }

            .details-grid {
                grid-template-columns: 1fr;
            }

            .button-group {
                grid-template-columns: 1fr;
            }

            .form-list {
                grid-template-columns: 1fr;
            }

            .form-details-header {
                flex-direction: column;
                text-align: center;
            }

            .detail-photo {
                margin-right: 0;
                margin-bottom: 20px;
            }

            h1 {
                font-size: 24px;
            }

            .header-icon {
                font-size: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Form Input Section -->
        <div id="formSection" style="<?php echo $showFilled ? 'display: none;' : ''; ?>">
            <div class="header">
                <div class="header-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h1>Professional Profile Form</h1>
                <p class="subtitle">Complete your profile with all necessary information</p>
            </div>

            <?php if (isset($_GET['success']) && $_GET['success'] === '1'): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> Form saved successfully!
                </div>
            <?php endif; ?>

            <form method="POST" action="save_form.php" enctype="multipart/form-data">
                <!-- Username -->
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Full Name *</label>
                    <div class="input-wrapper">
                        <input type="text" name="username" placeholder="John Doe" required>
                    </div>
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email Address *</label>
                    <div class="input-wrapper">
                        <input type="email" name="email" placeholder="john@example.com" required>
                    </div>
                </div>

                <!-- Contact Number -->
                <div class="form-group">
                    <label><i class="fas fa-phone"></i> Contact Number *</label>
                    <div class="input-wrapper">
                        <input type="text" name="contact_number" placeholder="+1 (555) 123-4567" required>
                    </div>
                </div>

                <!-- USN -->
                <div class="form-group">
                    <label><i class="fas fa-id-card"></i> USN/ID Number *</label>
                    <div class="input-wrapper">
                        <input type="text" name="usn" placeholder="24BTRXXXXX" required>
                    </div>
                </div>

                <!-- Address -->
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Address *</label>
                    <div class="input-wrapper">
                        <input type="text" name="address" placeholder="123 Main Street, City, State, Zip Code" required>
                    </div>
                </div>

                <!-- Gender -->
                <div class="form-group">
                    <label><i class="fas fa-venus-mars"></i> Gender *</label>
                    <div class="radio-group">
                        <div class="radio-item">
                            <input type="radio" id="male" name="gender" value="Male" required>
                            <label for="male">Male</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="female" name="gender" value="Female">
                            <label for="female">Female</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="other" name="gender" value="Other">
                            <label for="other">Other</label>
                        </div>
                    </div>
                </div>

                <!-- Languages -->
                <div class="form-group">
                    <label><i class="fas fa-code"></i> Technical Skills *</label>
                    <div class="checkbox-group">
                        <div class="checkbox-item">
                            <input type="checkbox" id="html" name="languages[]" value="HTML">
                            <label for="html">HTML</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="css" name="languages[]" value="CSS">
                            <label for="css">CSS</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="js" name="languages[]" value="JavaScript">
                            <label for="js">JavaScript</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="java" name="languages[]" value="Java">
                            <label for="java">Java</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="python" name="languages[]" value="Python">
                            <label for="python">Python</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" id="rust" name="languages[]" value="Rust">
                            <label for="rust">Rust</label>
                        </div>
                    </div>
                </div>

                <!-- Date of Birth -->
                <div class="form-group">
                    <label><i class="fas fa-birthday-cake"></i> Date of Birth *</label>
                    <div class="input-wrapper">
                        <input type="date" name="dob" required>
                    </div>
                </div>

                <!-- Photo Upload -->
                <div class="form-group">
                    <label><i class="fas fa-camera"></i> Profile Photo *</label>
                    <div class="input-wrapper">
                        <input type="file" name="photo" accept="image/*" required id="photoInput">
                        <div class="file-preview" id="photoPreview"></div>
                    </div>
                </div>

                <!-- Resume Upload -->
                <div class="form-group">
                    <label><i class="fas fa-file-pdf"></i> Resume/CV *</label>
                    <div class="input-wrapper">
                        <input type="file" name="resume" accept=".pdf,.doc,.docx" required id="resumeInput">
                        <div class="file-preview" id="resumePreview"></div>
                    </div>
                </div>

                <!-- Description -->
                <div class="form-group">
                    <label><i class="fas fa-pen-fancy"></i> About Yourself *</label>
                    <textarea name="description" placeholder="Tell us about yourself, your experience, and career goals..." required></textarea>
                </div>

                <!-- Buttons -->
                <div class="button-group">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Save Profile
                    </button>
                    <button type="button" class="btn-display" onclick="displayFilledForms()">
                        <i class="fas fa-eye"></i> View Saved Profiles
                    </button>
                </div>
            </form>
        </div>

        <!-- Filled Forms Display Section -->
        <div id="filledFormsSection" class="filled-forms-container" style="<?php echo $showFilled ? 'display: block;' : 'display: none;'; ?>">
            <div class="header">
                <div class="header-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h1>Saved Profiles</h1>
                <p class="subtitle">View and manage all your saved profiles</p>
            </div>

            <?php if (empty($savedForms)): ?>
                <div class="empty-message">
                    <div class="empty-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <p>No profiles saved yet. Create one to get started!</p>
                </div>
            <?php else: ?>
                <div class="form-list">
                    <?php foreach ($savedForms as $index => $form): ?>
                        <div class="form-card" onclick="viewForm(<?php echo $index; ?>)">
                            <?php if (!empty($form['photo_path']) && file_exists($form['photo_path'])): ?>
                                <img src="<?php echo htmlspecialchars($form['photo_path']); ?>" alt="Profile" class="card-photo">
                            <?php endif; ?>
                            <h3>
                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($form['username']); ?>
                            </h3>
                            <p>
                                <i class="fas fa-envelope"></i> <?php echo htmlspecialchars($form['email']); ?>
                            </p>
                            <p>
                                <i class="fas fa-id-card"></i> <?php echo htmlspecialchars($form['usn']); ?>
                            </p>
                            <p>
                                <i class="fas fa-calendar"></i> <?php echo date('M d, Y h:i A', $form['timestamp']); ?>
                            </p>
                            <p style="color: #667eea; margin-top: 15px; cursor: pointer; font-weight: 600;">
                                <i class="fas fa-arrow-right"></i> View Full Profile
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <button type="button" class="btn-back" onclick="backToForm()">
                <i class="fas fa-arrow-left"></i> Back to Form
            </button>
        </div>
    </div>

    <script>
        // File preview functionality
        document.getElementById('photoInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('photoPreview');
            if (file) {
                preview.textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)';
            }
        });

        document.getElementById('resumeInput')?.addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('resumePreview');
            if (file) {
                preview.textContent = '✓ ' + file.name + ' (' + (file.size / 1024).toFixed(2) + ' KB)';
            }
        });

        function displayFilledForms() {
            document.getElementById('formSection').style.display = 'none';
            document.getElementById('filledFormsSection').style.display = 'block';
            window.history.pushState({}, '', '?show=filled');
            window.scrollTo(0, 0);
        }

        function backToForm() {
            document.getElementById('formSection').style.display = 'block';
            document.getElementById('filledFormsSection').style.display = 'none';
            window.history.pushState({}, '', '?');
            const successMsg = document.querySelector('.success-message');
            if (successMsg) {
                successMsg.style.display = 'none';
            }
            window.scrollTo(0, 0);
        }

        function viewForm(index) {
            const forms = <?php echo json_encode($savedForms); ?>;
            if (forms[index]) {
                const form = forms[index];
                const photoHtml = form.photo_path ? `<img src="${form.photo_path}" alt="Profile" class="detail-photo">` : '';
                
                const details = `
                    <div class="form-details">
                        <div class="form-details-header">
                            ${photoHtml}
                            <div class="detail-header-text">
                                <h2>${form.username}</h2>
                                <p>
                                    <i class="fas fa-calendar-alt"></i>
                                    Saved on ${new Date(form.timestamp * 1000).toLocaleString()}
                                </p>
                            </div>
                        </div>
                        
                        <div class="details-grid">
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-envelope"></i> Email</div>
                                <div class="detail-value">${form.email}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-phone"></i> Contact Number</div>
                                <div class="detail-value">${form.contact_number}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-id-card"></i> USN</div>
                                <div class="detail-value">${form.usn}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-map-marker-alt"></i> Address</div>
                                <div class="detail-value">${form.address}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-venus-mars"></i> Gender</div>
                                <div class="detail-value">${form.gender}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-birthday-cake"></i> Date of Birth</div>
                                <div class="detail-value">${form.dob}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-code"></i> Technical Skills</div>
                                <div class="detail-value">${form.languages.join(', ')}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label"><i class="fas fa-file"></i> Resume</div>
                                <div class="detail-value">
                                    ${form.resume_path ? '<a href="' + form.resume_path + '" class="resume-link" download><i class="fas fa-download"></i> Download Resume</a>' : 'N/A'}
                                </div>
                            </div>
                            <div class="detail-item detail-item-full">
                                <div class="detail-label"><i class="fas fa-pen-fancy"></i> About</div>
                                <div class="detail-value">${form.description}</div>
                            </div>
                        </div>
                    </div>
                `;
                const formList = document.querySelector('.form-list');
                formList.insertAdjacentHTML('beforebegin', details);
                formList.style.display = 'none';
            }
        }
    </script>
</body>
</html>
