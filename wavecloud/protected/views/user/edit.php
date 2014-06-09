<script type="text/javascript">
$(function(){
    $("#submit-form").click(function(){
        var username = $("#username").val();
        if(!username){
            warningShowBox('请输入用户名！','warning');
            return false;
        }
        var groupid = $("#groupid").val();
        if(groupid == 0){
            warningShowBox('请选择用户组！','warning');
            return false;
        }
        warningHideBox('warning');
        $.ajax({
            type: "POST",
            url: "<?php echo Yii::app()->homeUrl.'/user/modify'; ?>",
            data: $("#form").serialize(),
            dataType:"json",
            success: function(json){
                if(json.success == true){
                    window.parent.fetchData();
                    $('.close').trigger("click");
                    window.parent.checkboxStatus();
                }else{
                    warningShowBox(json.msg,'warning');
                }
            }
        });
    })
})
</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3><?php echo empty($userinfo['id']) ? '添加' : '修改';?>用户</h3>
</div>
<form class="form-horizontal" id="form">
    <div class="modal-body">
        <div class="hide alert alert-error" id="warning"></div>
        <div class="control-group">
            <label class="control-label" for="username">用户名：</label>
            <div class="controls">
                <input type="text" name="user[username]" id="username" value="<?=$userinfo['username']?>" />
                <input type="hidden" id="userid" name="userid" value="<?=$userinfo['id']?>" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="email">邮箱：</label>
            <div class="controls">
                <input type="text" name="user[email]" id="email" value="<?=$userinfo['email']?>" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="email">用户组：</label>
            <div class="controls">
                <select name="user[group]" id="groupid">
                    <option value="0">请选择用户组</option>
                    <?php foreach ($garr as $key => $value): ?>
                        <option value="<?=$value['id']?>" <?php if($value['id'] == $userinfo['group']) echo "selected=true";?>>
                            <?=$value['group_name']?>
                        </option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="password">密码：</label>
            <div class="controls">
                <input type="password" name="user[password]" id="password" />
            </div>
        </div>
    </div>
</form>
<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn">关闭</button>
    <button id="submit-form" type="button" class="btn btn-primary">提交</button>
</div>
