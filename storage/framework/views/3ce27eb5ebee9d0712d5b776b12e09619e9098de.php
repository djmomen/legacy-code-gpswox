<!doctype html>
<html lang="<?php echo e(Language::iso(), false); ?>" class="no-js" itemscope itemtype="http://schema.org/WebSite">
<head>
    <?php echo $__env->make('Frontend.Layouts.partials.head', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script src="<?php echo e(asset_resource('assets/js/core.js'), false); ?>"></script>

    <?php echo $__env->yieldContent('styles'); ?>

    <style>
        <?php if( Appearance::getSetting('login_page_background_color') ): ?>
        body.sign-in-layout { background-color: <?php echo e(Appearance::getSetting('login_page_background_color'), false); ?>; }
        <?php endif; ?>

        <?php if( Appearance::getSetting('login_page_text_color') ): ?>
        body.sign-in-layout .sign-in-text { color: <?php echo e(Appearance::getSetting('login_page_text_color'), false); ?>; }
        <?php endif; ?>

        <?php if( Appearance::assetFileExists('background') ): ?>
        body.sign-in-layout { background-image: url( <?php echo Appearance::getAssetFileUrl('background'); ?> ); }
        <?php endif; ?>

        <?php if( Appearance::getSetting('login_page_panel_background_color') ): ?>
        body.sign-in-layout .panel-background { background: <?php echo e(Appearance::getSetting('login_page_panel_background_color'), false); ?>; }
        <?php endif; ?>

        <?php if( Appearance::getSetting('login_page_panel_transparency') != null ): ?>
        <?php $opacity = 1 - (int)Appearance::getSetting('login_page_panel_transparency') / 100; ?>
        body.sign-in-layout .panel-background { opacity: <?php echo e($opacity, false); ?>; }
        <?php endif; ?>
    </style>
</head>

<!--[if IE 8 ]><body class="ie8 sign-in-layout"> <![endif]-->
<!--[if IE 9 ]> <body class="ie9 sign-in-layout"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><body class="sign-in-layout"><!--<![endif]-->

<div class="center-vertical">
    <div class="container">
        <div class="col-xs-12 col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
</div>

</body>
</html><?php /**PATH C:\laragon6\www\saudix\Tobuli\Views/Frontend/Layouts/frontend.blade.php ENDPATH**/ ?>