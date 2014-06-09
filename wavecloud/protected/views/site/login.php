<style type="text/css">
    body {
        padding-top: 180px;
        padding-bottom: 40px;
        background-color: #f5f5f5;
    }

    #login-form {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
             -moz-border-radius: 5px;
                        border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
             -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                        box-shadow: 0 1px 2px rgba(0,0,0,.05);
    }
    #login-form .form-signin-heading,
    #login-form .checkbox {
        margin-bottom: 10px;
    }
    #login-form input[type="text"],
    #login-form input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
    }
</style>
<div class="container">
    <?php $form=$this->beginWidget('CActiveForm', array(
            'id'=>'login-form',
            'enableAjaxValidation'=>false,
            'htmlOptions'=>array('class'=>'form-horizontal'), 
    )); ?>
        <h2 class="form-signin-heading">登录 Wave Cloud</h2>
        <?php echo $form->error($model,'username',array('class'=>'alert alert-error show')); ?>
        <?php echo $form->error($model,'password',array('class'=>'alert alert-error show')); ?>
        <?php echo $form->textField($model,'username',array('class'=>'input-block-level','id'=>'username','required'=>'required','placeholder'=>'请输入用户名')); ?>
        <?php echo $form->passwordField($model,'password',array('class'=>'input-block-level','id'=>'password','required'=>'required','placeholder'=>'请输入密码')); ?>
        <button class="btn btn-primary" type="submit">登录</button>
    <?php $this->endWidget(); ?>
</div>