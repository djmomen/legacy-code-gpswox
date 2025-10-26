<?php $__env->startSection('content'); ?>
    <?php if( Appearance::getSetting('welcome_text') ): ?>
    <h1 class="sign-in-text text-center">
        <?php echo Appearance::getSetting('welcome_text'); ?>

    </h1>
    <?php endif; ?>

    <div class="panel">
        <div class="panel-background"></div>
        <div class="panel-body">

            <?php if( Appearance::assetFileExists('logo-main') ): ?>
            <a href="<?php echo e(route('home'), false); ?>">
                <img class="img-responsive center-block" src="<?php echo e(Appearance::getAssetFileUrl('logo-main'), false); ?>" alt="Logo">
            </a>
            <?php endif; ?>

            <hr>

            <?php if(Session::has('success')): ?>
                <div class="alert alert-success alert-dismissible">
                    <?php echo Session::get('success'); ?>

                </div>
            <?php endif; ?>

            <?php if(Session::has('message')): ?>
                <div class="alert alert-danger alert-dismissible">
                    <?php echo Session::get('message'); ?>

                </div>
            <?php endif; ?>

            <?php echo $__env->renderWhen(count($internalAuths), 'front::Login.partials.internal', $internalAuths, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>

            <?php if(count($externalAuths)): ?>
                <div class="row justify-content-center">
                    <h3 class="text-center"><?php echo e(trans('front.external_login'), false); ?></h3>

                    <?php $__currentLoopData = $externalAuths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $auth): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo $__env->make("front::Login.partials.{$auth->getKey()}", \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if( Appearance::getSetting('google_play_link') || Appearance::getSetting('apple_store_link') ): ?>
        <div class="app-links">
            <?php if( Appearance::getSetting('google_play_link') ): ?>
                <div class="col-xs-6">
                    <a href="<?php echo e(Appearance::getSetting('google_play_link'), false); ?>" target="_blank"><img src="<?php echo e(asset('assets/images/google-play.png'), false); ?>" class="img-responsive" /></a>
                </div>
            <?php endif; ?>

            <?php if( Appearance::getSetting('apple_store_link') ): ?>
                <div class="col-xs-6">
                    <a href="<?php echo e(Appearance::getSetting('apple_store_link'), false); ?>" target="_blank"><img src="<?php echo e(asset('assets/images/apple-store.png'), false); ?>" class="img-responsive" /></a>
                </div>
            <?php endif; ?>
            <div class="clearfix"></div>
        </div>
    <?php endif; ?>

    <?php if( Appearance::getSetting('bottom_text') ): ?>
        <p class="sign-in-text"><?php echo Appearance::getSetting('bottom_text'); ?></p>
    <?php endif; ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('Frontend.Layouts.frontend', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon6\www\saudix\Tobuli/Views/Frontend/Login/create.blade.php ENDPATH**/ ?>