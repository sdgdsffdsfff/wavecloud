<script type="text/javascript">
$(function(){
    $("#submit-form").click(function(){
        var groupname = $("#groupname").val();
        if(!groupname){
            warningShowBox('请输入用户组名！', 'warning');
            return false;
        }
        warningHideBox('warning');
        $.ajax({
            type: "POST",
            url: "<?php echo Yii::app()->homeUrl.'/group/modify'; ?>",
            data: $("#form").serialize(),
            dataType:"json",
            success: function(json){
                if(json.success == true){
                    window.parent.fetchData();
                    $('.close').trigger("click");
                }else{
                    warningShowBox(json.msg, 'warning');
                }
            }
        });
    })
})
</script>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3><?php echo empty($userinfo['id']) ? '添加' : '修改';?>用户组</h3>
</div>
<form class="form-horizontal" id="form">
    <div class="modal-body">
        <div class="hide alert alert-error" id="warning"></div>
        <div class="control-group">
            <label class="control-label" for="username">用户组名：</label>
            <div class="controls">
                <input type="text" name="group_name" id="groupname" value="<?=$garr['group_name']?>" />
                <input type="hidden" id="groupid" name="groupid" value="<?=$garr['id']?>" />
            </div>
        </div>
    </div>
</form>
<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn">关闭</button>
    <button id="submit-form" type="button" class="btn btn-primary">提交</button>
</div>