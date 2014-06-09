<?php $homeUrl = Yii::app()->homeUrl;?>
<script type="text/javascript">
$(function(){
    $("#submit-userform").click(function(){
        var oldpwd = $("#oldpwd").val();
        if(!oldpwd){
            warningShowBox("请输入旧密码！", 'warning');
            return false;
        }
        var newpwd = $("#newpwd").val();
        if (!newpwd) {
            warningShowBox("请输入新密码！", 'warning');
            return false;
        }
        warningHideBox('warning');
        $.ajax({
            type: "POST",
            url: "<?php echo $homeUrl.'/user/pwdmodify'; ?>",
            data: $("#userform").serialize(),
            dataType:"json",
            success: function(json){
                if(json.success == true){
                    window.location.href="<?php echo $homeUrl.'/user/index'; ?>";
                }else{
                    warningShowBox(json.msg, 'warning');
                }
            }
        });
    })
})
</script>
<div class="row-fluid">
    <div class="span2">
        <ul class="nav nav-pills nav-stacked" id="main-menu">
            <li class="active">
                <a href="<?php echo $homeUrl.'/user/index'; ?>">个人信息</a>
            </li>
            <?php if($group == 1):?>
                <li>
                    <a href="<?php echo $homeUrl.'/user/list'; ?>">用户列表</a>
                </li>
                <li>
                    <a href="<?php echo $homeUrl.'/group/index'; ?>">用户组列表</a>
                </li>
            <?php endif?>
        </ul>
    </div>
    <div class="span10">
        <div class="row-fluid">
            <div class="span12">
                <div class="box">
                    <h4 class="box-header round-top">首页 &gt; 个人信息</h4>
                    <div class="box-container-toggle">
                        <div class="box-content">
                            <form class="form-horizontal user-list-form">
                                <div class="control-group username-group">
                                    <label class="control-label">用户名：</label>
                                    <div class="controls controlsdiv">
                                        <div class="username-input"><?=$username?></div>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label" for="oldpwd">所属组：</label>
                                    <div class="controls controlsdiv">
                                        <?=$groupname?>
                                    </div>
                                </div>
                                <div class="control-group">
                                    <label class="control-label"></label>
                                    <div class="controls">
                                        <a href="#pwdpage" data-toggle="modal" class="btn btn-primary modify-btn">
                                            <i class="icon-edit icon-white"></i> 修改密码
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="pwdpage" class="modal hide fade" tabindex="-1" data-width="560">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h3>修改密码</h3>
    </div>
    <form class="form-horizontal" method="post" id="userform">
        <div class="modal-body">
            <div class="hide alert alert-error" id="warning"></div>
            <div class="control-group username-group">
                <label class="control-label">用户名：</label>
                <div class="controls controlsdiv">
                    <div class="username-input"><?=$username?></div>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="oldpwd">旧密码：</label>
                <div class="controls">
                    <input type="password" name="oldpwd" id="oldpwd" placeholder="请输入旧密码" required="required" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="newpwd">新密码：</label>
                <div class="controls">
                    <input type="password" name="newpwd" id="newpwd" placeholder="请输入新密码" required="required" />
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" data-dismiss="modal" class="btn">关闭</button>
            <button id="submit-userform" type="button" class="btn btn-primary"><i class="icon-edit icon-white"></i> 提交</button>
        </div>
    </form>
</div>
