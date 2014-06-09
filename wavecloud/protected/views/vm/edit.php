<script type="text/javascript">
$(function(){
    $("#submit-form").click(function(){
        var vm_name = $("#vm_name").val();
        if(!vm_name){
            warningShowBox('请输入虚拟机名称！','warning');
            return false;
        }
        var vm_num = $("#vm_num").val();
        if(!vm_num){
            warningShowBox('请输入虚拟机数量！','warning');
            return false;
        }
        warningHideBox('warning');
        $.ajax({
            type: "POST",
            url: "<?php echo Yii::app()->homeUrl.'/vm/add'; ?>",
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
    <h3><?php echo empty($arr['vm_id']) ? '添加' : '修改';?>虚拟机</h3>
</div>
<form class="form-horizontal" id="form">
    <div class="modal-body">
        <div class="hide alert alert-error" id="warning"></div>
        <div class="control-group">
            <label class="control-label" for="vm_name">虚拟机名称：</label>
            <div class="controls">
                <input type="text" name="vm_name" id="vm_name" value="<?=$arr['vm_name']?>" />
                <input type="hidden" id="vm_id" name="vm_id" value="<?=$arr['vm_id']?>" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="vm_name">虚拟机数量：</label>
            <div class="controls">
                <input type="text" name="vm_num" id="vm_num" />
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="net_id">选择模版：</label>
            <div class="controls">
                <select id="template_id" name="template_id">
                    <?php foreach ($templatearr as $key => $value):?>
                    <option value="<?=$value['template_id']?>" <?php if($arr['template_id'] == $value['template_id']):?> selected <?php endif;?>>
                        <?=$value['template_name']?>
                    </option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
    </div>
</form>
<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn">关闭</button>
    <button id="submit-form" type="button" class="btn btn-primary">提交</button>
</div>
