<script type="text/javascript">
$(function(){
    $("#submit-form").click(function(){
        var pm_name = $("#pm_name").val();
        if(!pm_name){
            warningShowBox('请输入物理机名称！','warning');
            return false;
        }
        var pm_ip = $("#pm_ip").val();
        if(!pm_ip){
            warningShowBox('请输入物理机IP！','warning');
            return false;
        }
        warningHideBox('warning');
        $.ajax({
            type: "POST",
            url: "<?php echo Yii::app()->homeUrl.'/pm/modify'; ?>",
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
    <h3><?php echo empty($arr['pm_id']) ? '添加' : '修改';?>物理机</h3>
</div>
<form class="form-horizontal" id="form">
    <div class="modal-body">
        <div class="hide alert alert-error" id="warning"></div>
        <div class="control-group">
            <label class="control-label" for="pm_name">物理机名称：</label>
            <div class="controls">
                <input type="text" name="pm_name" id="pm_name" value="<?=$arr['pm_name']?>" />
                <input type="hidden" id="pm_id" name="pm_id" value="<?=$arr['pm_id']?>" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="pm_ip">物理机地址：</label>
            <div class="controls">
                <input type="text" name="pm_ip" id="pm_ip" value="<?=$arr['pm_ip']?>" />
            </div>
        </div>
    </div>
</form>
<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn">关闭</button>
    <button id="submit-form" type="button" class="btn btn-primary">提交</button>
</div>
