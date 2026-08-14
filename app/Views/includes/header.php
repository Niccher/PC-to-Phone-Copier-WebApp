<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>P2P Web Copier</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>" id="csrf-token">
    <meta name="csrf-header" content="<?= config('Security')->headerName ?? 'X-CSRF-TOKEN' ?>">

    <meta content="P2P Web Copier" name="description" />
    <meta content="Niccher Inc" name="author" />
    <!-- App favicon -->
    <link rel="icon" type="image/png" href="<?php echo base_url('favicon.png')?>">
    <link rel="apple-touch-icon" href="<?php echo base_url('favicon.png')?>">

    <!-- App css -->
    <link href="<?php echo base_url('assets/css/icons.min.css')?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/css/app.min.css')?>" rel="stylesheet" type="text/css" id="light-style" />
    <link href="<?php echo base_url('assets/css/app-dark.min.css')?>" rel="stylesheet" type="text/css" id="dark-style" />

    <link href="<?php echo base_url('assets/summernote/summernote-lite.min.css')?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/dropzone/dropzone.css')?>" rel="stylesheet" type="text/css" />
    <link href="<?php echo base_url('assets/css/custom.css')?>" rel="stylesheet" type="text/css" />
    
    <!-- QR Code Generator -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="<?php echo base_url('manifest.json') ?>">
    <script>
        let deferredPwaPrompt;
        
        window.addEventListener('beforeinstallprompt', (e) => {
            // Prevent Chrome from automatically showing the prompt
            e.preventDefault();
            // Stash the event so it can be triggered later.
            deferredPwaPrompt = e;
            // Show the Install Button in the sidebar
            const installBtn = document.getElementById('pwa-install-item');
            if (installBtn) installBtn.style.display = 'block';
        });

        window.triggerPWAInstall = () => {
            if (deferredPwaPrompt) {
                // Show the install prompt
                deferredPwaPrompt.prompt();
                // Wait for the user to respond to the prompt
                deferredPwaPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        console.log('User safely installed the web app');
                    }
                    // Reset the deferred prompt variable
                    deferredPwaPrompt = null;
                    // Hide the install button
                    const installBtn = document.getElementById('pwa-install-item');
                    if (installBtn) installBtn.style.display = 'none';
                });
            }
        };

        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?php echo base_url('service-worker.js') ?>')
                    .then(reg => console.log('SW correctly registered with scope:', reg.scope))
                    .catch(err => console.log('SW registration safely failed:', err));
            });
        }
    </script>
</head>
<body class="loading" data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
<!-- Begin page -->