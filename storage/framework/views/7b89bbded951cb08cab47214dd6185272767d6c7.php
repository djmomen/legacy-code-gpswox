<title><?php echo e(Appearance::getSetting('server_name'), false); ?></title>

<base href="<?php echo e(url('/'), false); ?>">
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="app-version" content="<?php echo e(config('tobuli.version'), false); ?>">
<meta name="app-build" content="<?php echo e(config('app.build'), false); ?>">
<meta name="csrf-token" content="<?php echo e(csrf_token(), false); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo e(Appearance::getSetting('server_description'), false); ?>">
<link rel="shortcut icon" href="<?php echo e(Appearance::getAssetFileUrl('favicon'), false); ?>" type="image/x-icon">
<link rel="stylesheet" href="<?php echo e(asset_resource('assets/css/'.Appearance::getSetting('template_color').'.css'), false); ?>">
<?php if(Language::dir() == 'rtl'): ?>
    <link rel="stylesheet" href="<?php echo e(asset_resource('assets/css/rtl.css'), false); ?>">
<?php endif; ?>
<?php if(Appearance::assetFileExists('css')): ?>
    <link rel="stylesheet" href="<?php echo e(Appearance::getAssetFileUrl('css'), false); ?>">
<?php endif; ?>
<?php if(Appearance::assetFileExists('js')): ?>
    <script src="<?php echo e(Appearance::getAssetFileUrl('js'), false); ?>" type="text/javascript" defer></script>
<?php endif; ?>
<?php /**PATH C:\laragon6\www\saudix\Tobuli\Views/Frontend/Layouts/partials/head.blade.php ENDPATH**/ ?>