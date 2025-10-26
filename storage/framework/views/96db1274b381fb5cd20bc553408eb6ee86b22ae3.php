<?php $__env->startSection('title', 'Page Not Found'); ?>

<?php $__env->startSection('message'); ?>
    <?php echo trans('errors.404'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('errors::layout', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\laragon6\www\saudix\resources\views/errors/404.blade.php ENDPATH**/ ?>