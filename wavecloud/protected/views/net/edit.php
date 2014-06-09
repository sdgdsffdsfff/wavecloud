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
    var radio = $("input[name='check_radio']:checked").val();
    if(radio == undefined){
        $("#check_radio_1").attr('checked', 'checked');
    }else{
        if(radio == 1){
            $("#fixed").css({"display":"block"});
            $("#ranged").css({"display":"none"});
        }else{
            $("#fixed").css({"display":"none"});
            $("#ranged").css({"display":"block"});
        }
    }
    $("input[name='check_radio']").click(function(){
        var radio = $(this).val();
        if(radio == 1){
            $("#fixed").css({"display":"block"});
            $("#ranged").css({"display":"none"});
        }else{
            $("#fixed").css({"display":"none"});
            $("#ranged").css({"display":"block"});
        }
    })
})

var checkForm = function(){
     var net_name = $("#net_name").val();
    if(!net_name){
        warningShowBox('请输入网络名称！','warning');
        return false;
    }
    warningHideBox('warning');

    return true;
}

var addIplist = function(){
    var ip = $("#ip").val();
    if(ip){
        $("#ip_multiple").append("<option value='"+ip+"'>"+ip+"</option>"); 
        var iplist = $("#iplist").val();
        if(iplist){
            $("#iplist").val(iplist+','+ip);
        }else{
            $("#iplist").val(ip);
        }
    }
}

var delIplist = function(){
    var iplist = $("#ip_multiple").val();
    for (var i = 0; i < iplist.length; i++) {
        $("#ip_multiple option[value='"+iplist[i]+"']").remove();
    };
}

var reloadData = function(txt){
    alert(txt);
    fetchData();
}

</script>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3><?php echo empty($arr['net_id']) ? '添加' : '修改';?>网络</h3>
</div>
<form class="form-horizontal" id="form" method="POST" action="<?php echo Yii::app()->homeUrl.'/net/modify'; ?>" onsubmit="return checkForm()">
    <div class="modal-body">
        <div class="hide alert alert-error" id="warning"></div>
            <div class="control-group">
                <label class="control-label" for="net_name">网络名称：</label>
                <div class="controls">
                    <input type="text" name="net_name" id="net_name" value="<?=$arr['net_name']?>" />
                    <input type="hidden" id="net_id" name="net_id" value="<?=$arr['net_id']?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="net_bridge">网桥名称：</label>
                <div class="controls">
                    <input type="text" name="net_bridge" id="net_bridge" value="<?=$arr['net_bridge']?>" />
                </div>
            </div>
        <fieldset>
            <legend>类型</legend>
            <div class="control-group">
                <label class="control-label" for="net_private">内网前缀：</label>
                <div class="controls">
                    <input type="text" name="net_private" id="net_private" value="<?=$arr['net_private']?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="net_gateway">网关：</label>
                <div class="controls">
                    <input type="text" name="net_gateway" id="net_gateway" value="<?=$arr['net_gateway']?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="net_mask">掩码：</label>
                <div class="controls">
                    <input type="text" name="net_mask" id="net_mask" value="<?=$arr['net_mask']?>" />
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="net_dns">DNS：</label>
                <div class="controls">
                    <input type="text" name="net_dns" id="net_dns" value="<?=$arr['net_dns']?>" />
                </div>
            </div>
            <div class="control-group radio-group clearfix">
                <label class="radio">
                    <input type="radio" id="check_radio_1" name="check_radio" value="1" <?php if($arr['check_radio'] == 1):?> checked <?php endif;?>> 固定式网络
                </label>
                <label class="radio">
                    <input type="radio" id="check_radio_2" name="check_radio" value="2" <?php if($arr['check_radio'] == 2):?> checked <?php endif;?>> 范围式网络
                </label>
            </div>
            <div id="fixed">
                <div class="control-group">
                    <label class="control-label" for="ip">IP：</label>
                    <div class="controls">
                        <input type="text" name="ip" id="ip" />
                        <button type="button" class="btn btn-info btn-small" onclick="addIplist()">添加</button>
                        <input type="hidden" id="iplist" name="iplist" value="<?=$ipliststr?>">
                    </div>
                </div>
                <div class="control-group">
                    <div class="controls">
                        <select multiple="multiple" id="ip_multiple" name="ip_list">
                            <?php foreach ($iplist as $key => $value):?>
                            <option value="<?=$value['ip']?>"><?=$value['ip']?></option>
                            <?php endforeach;?>
                        </select>
                        <button type="button" class="btn btn-danger btn-small" onclick="delIplist()">删除</button>
                    </div>
                </div>
            </div>
            <div id="ranged">
                <div class="control-group">
                    <label class="control-label" for="ip">IP起始：</label>
                    <div class="controls">
                        <input type="text" name="ip_begin" id="ip_begin" value="<?=$arr['ip_begin']?>" />
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="ip">IP结束：</label>
                    <div class="controls">
                        <input type="text" name="ip_end" id="ip_end" value="<?=$arr['ip_end']?>" />
                    </div>
                </div>
            </div>
        </fieldset>
    </div>
<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn">关闭</button>
    <button id="submit-form" type="submit" class="btn btn-primary">提交</button>
</div>
</form>