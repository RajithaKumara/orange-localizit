<form action="<?php echo url_for('@sign_in'); ?>" class="loginBar mediumText" method="post" id="sign_in_form" name="sign_in_form">
    <?php
    $signInForm = new SignInForm();
    echo $signInForm['_csrf_token']->render();
    ?>
    <?php echo $signInForm['loginName']->renderLabel( __('username', null, 'authenticationMessages'));?> &nbsp;<?php echo $signInForm['loginName']->render(); ?>
    <?php echo $signInForm['password']->renderLabel( __('password', null, 'authenticationMessages'));?> &nbsp;<?php  echo $signInForm['password']->render(); ?>
    <input type="button" name="login_label" id="login" class="button normalText" value="<?php echo __('login', null , 'authenticationMessages'); ?>" />
</form>