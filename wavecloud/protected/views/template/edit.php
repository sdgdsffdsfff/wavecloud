<script type="text/javascript">
$(function(){
    // $("#submit-form").click(function(){
    //     $.ajax({
    //         type: "POST",
    //         url: "<?php echo Yii::app()->homeUrl.'/net/modify'; ?>",
    //         data: $("#form").serialize(),
    //         dataType:"json",
    //         success: function(json){
    //             if(json.success == true){
    //                 window.parent.fetchData();
    //                 $('.close').trigger("click");
    //                 window.parent.checkboxStatus();
    //             }else{
    //                 warningShowBox(json.msg,'warning');
    //             }
    //         }
    //     });
    // })
    $('#sl1').slider({
        formater: function(value) {
            return value+'%';
        }
    });
    $("#sl1").on('slideStop', function(slideEvt) {
        $("#percentage").val(slideEvt.value);
    });

    var radio = $("input[name='partition']:checked").val();
    if(radio == undefined){
        $("#check_radio_0").attr('checked', 'checked');
    }else{
        if(radio == 0){
            $("#partition-div").css({"display":"block"});
        }else{
            $("#partition-div").css({"display":"none"});
        }
    }

    $("input[name='partition']").click(function(){
        var radio = $(this).val();
        if(radio == 0){
            $("#partition-div").css({"display":"block"});
        }else{
            $("#partition-div").css({"display":"none"});
        }
    })

})

var checkForm = function(){
     var template_name = $("#template_name").val();
    if(!template_name){
        warningShowBox('请输入模板名称！','warning');
        return false;
    }
    warningHideBox('warning');

    return true;
}

var reloadData = function(txt){
    fetchData();
}

</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3><?php echo empty($arr['template_id']) ? '添加' : '修改';?>模版</h3>
</div>
<form class="form-horizontal" id="form" method="POST" action="<?php echo Yii::app()->homeUrl.'/template/modify'; ?>" onsubmit="return checkForm()">
<div class="box-header">
    <div class="hide alert alert-error" id="warning"></div>
    <ul  class="nav nav-tabs">
        <li class="active"><a href="#t-home" data-toggle="tab">基本选项</a></li>
        <li><a href="#t-pm" data-toggle="tab">选择物理机</a></li>
    </ul>
</div>
<div class="tab-content">
    <div class="tab-pane active" id="t-home">
        <div class="modal-body">
            <div class="control-group">
                <label class="control-label" for="template_name">模版名称：</label>
                <div class="controls">
                    <input type="text" name="template_name" id="template_name" value="<?=$arr['template_name']?>" />
                    <input type="hidden" id="template_id" name="template_id" value="<?=$arr['template_id']?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="iso_id">选择镜像：</label>
                <div class="controls">
                    <select id="iso_id" name="iso_id">
                        <?php foreach ($infoarr['iso'] as $key => $value):?>
                        <option value="<?=$value['iso_id']?>" <?php if($arr['iso_id'] == $value['iso_id']):?> selected <?php endif;?>><?=$value['iso_name']?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="net_id">选择网络：</label>
                <div class="controls">
                    <select id="iso_id" name="net_id">
                        <?php foreach ($infoarr['net'] as $key => $value):?>
                        <option value="<?=$value['net_id']?>" <?php if($arr['net_id'] == $value['net_id']):?> selected <?php endif;?>><?=$value['net_name']?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="cpu">CPU核心数：</label>
                <div class="controls">
                    <select id="cpu" name="cpu">
                        <?php foreach ($infoarr['cpu'] as $key => $value):?>
                        <option value="<?=$key?>" <?php if($arr['cpu'] == $key):?> selected <?php endif;?>><?=$value?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="mem">内存(G)：</label>
                <div class="controls">
                    <select id="mem" name="mem">
                        <?php foreach ($infoarr['mem'] as $key => $value):?>
                        <option value="<?=$key?>" <?php if($arr['mem'] == $key):?> selected <?php endif;?>><?=$value?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="hd">硬盘(G)：</label>
                <div class="controls">
                    <select id="hd" name="hd">
                        <?php foreach ($infoarr['hd'] as $key => $value):?>
                        <option value="<?=$key?>" <?php if($arr['hd'] == $key):?> selected <?php endif;?>><?=$value?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </div>
            <div class="control-group">
                <label class="control-label">data盘是否分区：</label>
                <div class="controls" id="data-partition">
                    <input type="radio" class="data-radio" id="check_radio_0" name="partition" value="0" <?php if($arr['partition'] == 0):?> checked <?php endif;?>> 是
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <input type="radio" class="data-radio" name="partition" value="1" <?php if($arr['partition'] == 1):?> checked <?php endif;?>> 否
                </div>
            </div>
            <div class="control-group" id="partition-div">
                <span style="margin-left:12%;">home</span>
                <input id="sl1" class="span2" type="text" data-slider-value="<?php echo isset($arr['percentage']) ? $arr['percentage'] : 20;?>" data-slider-step="1" data-slider-max="90" data-slider-min="5">
                <input type="hidden" value="<?php echo isset($arr['percentage']) ? $arr['percentage'] : 20;?>" name="percentage" id="percentage">
                <span>data</span>
            </div>
        </div>
    </div>
    <div class="tab-pane" id="t-pm">
        <label>选择物理机：(注意，请选择同一网关的物理机)</label>
        <table class="table table-bordered table-striped table-hover" id="tabledata">
            <thead>
                <tr>
                    <th style="width: 15px;"></th>
                    <th>名称</th>
                    <th>IP</th>
                    <th>网关</th>
                    <th>是否作为镜像节点</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($infoarr['pm'] as $key => $value):?>
                <tr>
                    <td>
                        <input type="checkbox" name="pm_ids[]" value="<?=$value['pm_id']?>" <?php if(isset($value['checked'])):?> checked <?php endif;?> >
                    </td>
                    <td><?=$value['pm_name']?></td>
                    <td><?=$value['pm_ip']?></td>
                    <td><?=$value['public_gateway']?></td>
                    <td>
                        <input type="radio" name="iso_node_id" value='<?=$value['pm_id']?>' <?php if($arr['iso_node_id'] == $value['pm_id']):?> checked <?php endif;?>>
                    </td>
                </tr>
                <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn">关闭</button>
    <button id="submit-form" type="submit" class="btn btn-primary">提交</button>
</div>
</form>