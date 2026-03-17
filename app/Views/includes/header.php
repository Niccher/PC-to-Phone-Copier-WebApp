<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>P2P Web Copier</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="P2P Web Copier" name="description" />
    <meta content="Niccher Inc" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.ico')?>">

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
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('<?php echo base_url('service-worker.js') ?>')
                    .then(reg => console.log('SW registered'))
                    .catch(err => console.log('SW registration failed', err));
            });
        }
    </script>
</head>
<body class="loading" data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":false, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
<!-- Begin page -->