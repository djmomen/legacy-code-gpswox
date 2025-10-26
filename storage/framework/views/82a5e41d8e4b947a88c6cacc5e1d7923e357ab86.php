<?php echo Form::open(array('route' => 'authentication.store', 'class' => 'form')); ?>


<?php /** @var \Tobuli\Services\Auth\InternalInterface[] $internalAuths */?>

<div class="form-group">
    <?php echo Form::text(
        'identifier',
        null,
        [
            'class' => 'form-control',
            'placeholder' => implode(' / ', array_map(fn ($auth) => $auth->getInputTitle(), $internalAuths)),
            'id' => 'sign-in-form-email',
        ]
    ); ?>

</div>

<div class="form-group">
    <?php echo Form::password('password', ['class' => 'form-control', 'placeholder' => trans('validation.attributes.password'), 'id' => 'sign-in-form-password']); ?>

</div>

<?php echo $__env->make('Frontend.Captcha.form', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php if(config('session.remember_me')): ?>
    <div class="form-group">
        <div class="checkbox">
            <?php echo Form::checkbox('remember_me', 1, ['id' => 'sign-in-form-remember']); ?>

            <label><?php echo trans('validation.attributes.remember_me'); ?></label>
        </div>
    </div>
<?php endif; ?>

<button class="btn btn-lg btn-primary btn-block" name="Submit" value="Login" type="Submit"><?php echo trans('front.sign_in'); ?></button>

<hr>

<div class="form-group">
    <div class="row">
        <div class="col-sm-12">
            <a href="<?php echo route('password_reminder.create'); ?>"
               class="btn btn-block btn-lg btn-default"><?php echo trans('front.cant_sign_in'); ?></a>
        </div>
        <div class="col-sm-12">
            <?php if(settings('main_settings.allow_users_registration')): ?>
                <a href="<?php echo route('registration.create'); ?>"
                   class="btn btn-block btn-lg btn-default"><?php echo trans('front.not_a_member'); ?></a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php echo Form::close(); ?>

<?php /**PATH C:\laragon6\www\saudix\Tobuli/Views/Frontend/Login/partials/internal.blade.php ENDPATH**/ ?>