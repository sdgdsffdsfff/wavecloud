<script type="text/javascript">
$(function(){
    $("#submit-form").click(function(){
        var iso_name = $("#iso_name").val();
        if(!iso_name){
            warningShowBox('请输入镜像名称！','warning');
            return false;
        }
        var iso_path = $("#iso_path").val();
        if(!iso_path){
            warningShowBox('请输入镜像路径！','warning');
            return false;
        }
        warningHideBox('warning');
        $.ajax({
            type: "POST",
            url: "<?php echo Yii::app()->homeUrl.'/iso/modify'; ?>",
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
    <h3><?php echo empty($arr['iso_id']) ? '添加' : '修改';?>镜像</h3>
</div>
<form class="form-horizontal" id="form">
    <div class="modal-body">
        <div class="hide alert alert-error" id="warning"></div>
        <div class="control-group">
            <label class="control-label" for="iso_name">镜像名称：</label>
            <div class="controls">
                <input type="text" name="iso_name" id="iso_name" value="<?=$arr['iso_name']?>" />
                <input type="hidden" id="iso_id" name="iso_id" value="<?=$arr['iso_id']?>" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="iso_path">镜像地址：</label>
            <div class="controls">
                <input type="text" name="iso_path" id="iso_path" value="<?=$arr['iso_path']?>" />
            </div>
        </div>
    </div>
</form>
<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn">关闭</button>
    <button id="submit-form" type="button" class="btn btn-primary">提交</button>
</div>
