<div class="messageBar loginpage">
        <?php if($sf_user->getFlash('errorMessage') != '') { ?>
            <span class="error"><?php echo $sf_user->getFlash('errorMessage'); ?></span>
        <?php } else if($sf_user->getFlash('successMessage') != '') { ?>
            <span class="success"><?php echo $sf_user->getFlash('successMessage');?></span>
        <?php } ?>
</div>
<div class="outerBorder loginBorderOuterFrame">
<div class="loginForm">
    <form action="<?php echo url_for('@sign_in'); ?>" method="post" id="sign_in_form" name="sign_in_form">
        <?php echo $signInForm->renderHiddenFields(); ?>
        <div class="mainLogo">
            <?php echo __("orange_localizit", null, 'authenticationMessages'); ?>
        </div>
        <table class="mediumText loginFormBackGround">
             <tr>
                <td >
                    <?php echo $signInForm['loginName']->renderLabel( __('username', null, 'authenticationMessages')) ?>
                </td>
                <td class="addDotLinetoRight">
                    <?php echo $signInForm['loginName']->render() ?>
                    <div class="errorMsg">
                <?php if ($signInForm['loginName']->hasError()) { ?>
                      <?php echo $signInForm['loginName']->renderError() ?>
                <?php } ?>
                </div>
                </td>
            </tr>
            <tr>
                <td>
                    <?php echo $signInForm['password']->renderLabel( __('password', null, 'authenticationMessages')) ?>
                </td>
                <td class="addDotLinetoRight">
                    <?php echo $signInForm['password']->render() ?>
                <div class="errorMsg">
                <?php if ($signInForm['password']->hasError()) { ?>
                    <?php echo $signInForm['password']->renderError() ?>
                <?php } ?>
                </div></td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td class="addDotLinetoRight">
                    <input type="button" name="login_label" id="login" class="button normalText" value="<?php echo __('login', null , 'authenticationMessages') ?>" />
                    <input type="reset" name="cancel_label" id="cancel_label" class="button normalText" onclick="redircetToPage('<?php echo url_for("@homepage")?>')" value="<?php echo __('cancel', null , 'authenticationMessages') ?>" />
                </td>
                <?php if ($signInForm['password']->hasError() || $signInForm['loginName']->hasError()) { ?>
                <?php } ?>
            </tr>
        </table>
    </form>
</div>
</div>
