<?php $homeUrl = Yii::app()->homeUrl;?>
<script type="text/javascript">
var fetchData = function(){
    listData({
        url : "<?php echo $homeUrl.'/net/list'; ?>",
        columns: [
            { "mData": "checkbox"},
            { "mData": "net_id"},
            { "mData": "net_name"},
            { "mData": "net_bridge"},
            { "mData": "net_gateway"},
            { "mData": "net_mask"},
            { "mData": "net_dns"},
            { "mData": "username"},
            { "mData": "operat"}
        ],
        tableId : 'tabledata',
        paginate : true,
        serverside : true
    });
}

$(function(){
    fetchData();
})

var lookIp = function(url){
    var $modal = $('#ajax-modal');
    $('body').modalmanager('loading');
    setTimeout(function(){
        $modal.load(url, '', function(){
            $modal.modal();
        });
    }, 50);
}

</script>
<div class="span10">
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <h4 class="box-header round-top">首页 &gt; 网络管理</h4>
                <div class="box-container-toggle">
                    <div class="box-content">
                        <div class="clearfix">
                            <button class="btn btn-small" onclick="fetchData()">
                                <i class="icon-repeat"></i> 刷新
                            </button>
                            <button class="btn btn-small btn-info" onclick="editFunc('<?php echo Yii::app()->homeUrl; ?>/net/edit/id/0')">
                                <i class="icon-plus icon-white"></i> 添加
                            </button>
                            <button class="btn btn-small btn-danger btnchose disabled" id="delete" onclick="deleteFunc('<?php echo $homeUrl; ?>/net/delete')">
                                <i class="icon-trash icon-white"></i> 删除 
                            </button>
                        </div>
                        <div class="list">
                            <table class="table table-bordered table-striped table-hover" id="tabledata">
                                <thead>
                                    <tr>
                                        <th style="width: 15px;">
                                            <input type="checkbox" onclick="checkboxAll(this)">
                                        </th>
                                        <th style="width: 40px;">ID</th>
                                        <th>名称</th>
                                        <th>网桥</th>
                                        <th>网关</th>
                                        <th>掩码</th>
                                        <th>DNS</th>
                                        <th>创建用户</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="9" class="dataTables_empty">Loading data from server</td>
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