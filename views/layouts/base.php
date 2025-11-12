<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->e($title ?? 'Findik Engine') ?></title>
    <script src="https://cdn.tailwindcss.com" nonce="<?= $_SESSION['csp_nonce'] ?? '' ?>"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" nonce="<?= $_SESSION['csp_nonce'] ?? '' ?>" />
</head>
<body class="bg-gray-100">
    <?= $this->section('body') ?>
    
    <script nonce="<?= $_SESSION['csp_nonce'] ?? '' ?>">
        // Mobile menu toggle
        function toggleMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }
    </script>
</body>
</html>
