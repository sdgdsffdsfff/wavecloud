<?php $homeUrl = Yii::app()->homeUrl;?>
<script type="text/javascript">
var fetchData = function(){
    listData({
        url : "<?php echo $homeUrl.'/vm/list'; ?>",
        columns: [
            { "mData": "checkbox"},
            { "mData": "vm_id"},
            { "mData": "username"},
            { "mData": "vm_name"},
            { "mData": "status"},
            { "mData": "pm_ip"},
            { "mData": "ips"},
            { "mData": "operating"},
            { "mData": "vnc"}
        ],
        tableId : 'tabledata',
        paginate : true,
        serverside : true
    });
}

$(function(){
    fetchData();
})

</script>
<div class="span10">
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <h4 class="box-header round-top">首页 &gt; 虚拟机管理</h4>
                <div class="box-container-toggle">
                    <div class="box-content">
                        <div class="clearfix">
                            <div class="pull-left">
                                <button class="btn btn-small" onclick="fetchData()">
                                    <i class="icon-repeat"></i> 刷新
                                </button>
                                <button class="btn btn-small btn-info" onclick="editFunc('<?php echo Yii::app()->homeUrl; ?>/vm/edit/id/0')">
                                    <i class="icon-plus icon-white"></i> 添加
                                </button>
                                <button class="btn btn-small btn-danger btnchose disabled" id="delete" onclick="deleteFunc('<?php echo $homeUrl; ?>/vm/delete')">
                                    <i class="icon-trash icon-white"></i> 删除 
                                </button>
                            </div>
                            <div class="pull-right">
                                <button class=" btn btn-small btn-success btnchose disabled">
                                    <i class="icon-play icon-white"></i> 开机 
                                </button>
                                <button class="btn btn-small btn-info btnchose disabled">
                                    <i class="icon-repeat icon-white"></i> 重启 
                                </button>
                                <button class="btn btn-small btn-danger btnchose disabled">
                                    <i class="icon-off icon-white"></i> 关机 
                                </button>
                                <button class="btn btn-small btn-primary btnchose disabled">
                                    <i class="icon-th-large icon-white"></i> 重装 
                                </button>
                                <button class="btn btn-small btn-inverse btnchose disabled">
                                    <i class="icon-plus icon-white"></i> 添加磁盘 
                                </button>
                            </div>
                        </div>
                        <div class="list">
                            <table class="table table-bordered table-striped table-hover" id="tabledata">
                                <thead>
                                    <tr>
                                        <th style="width: 15px;">
                                            <input type="checkbox" onclick="checkboxAll(this)">
                                        </th>
                                        <th style="width: 40px;">ID</th>
                                        <th>创建用户</th>
                                        <th>名称</th>
                                        <th>状态</th>
                                        <th>宿主机</th>
                                        <th>IPs</th>
                                        <th>操作</th>
                                        <th>VNC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7" class="dataTables_empty">Loading data from server</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="ajax-modal" class="modal hide fade" tabindex="-1"></div>