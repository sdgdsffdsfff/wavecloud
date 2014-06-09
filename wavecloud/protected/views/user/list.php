<?php $homeUrl = Yii::app()->homeUrl;?>
<script type="text/javascript">
var fetchData = function(){
    listData({
        url : "<?php echo $homeUrl.'/user/listjson'; ?>",
        columns: [
            { "mData": "checkbox"},
            { "mData": "id"},
            { "mData": "username"},
            { "mData": "email"},
            { "mData": "group"},
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
</script>
<input type="hidden" id="groupshow" value="<?=$group?>">
<div class="row-fluid">
    <div class="span2">
        <ul class="nav nav-pills nav-stacked" id="main-menu">
            <li>
                <a href="<?php echo Yii::app()->homeUrl.'/user/index'; ?>">个人信息</a>
            </li>
            <li class="active">
                <a href="<?php echo Yii::app()->homeUrl.'/user/list'; ?>">用户列表</a>
            </li>
            <li>
                <a href="<?php echo Yii::app()->homeUrl.'/group/index'; ?>">用户组列表</a>
            </li>
        </ul>
    </div>    
    <div class="span10">
        <div class="row-fluid">
            <div class="span12">
                <div class="box">
                    <h4 class="box-header round-top">首页 &gt; 用户列表</h4>
                    <div class="box-container-toggle">
                        <div class="box-content">
                            <div class="clearfix">
                                <button class="btn btn-small" onclick="fetchData()">
                                    <i class="icon-repeat"></i> 刷新
                                </button>
                                <button class="btn btn-small btn-info" onclick="editFunc('<?php echo $homeUrl; ?>/user/edit/id/0')">
                                    <i class="icon-plus icon-white"></i> 添加
                                </button>
                                <button class="btn btn-small btn-danger btnchose disabled" id="delete" onclick="deleteFunc('<?php echo $homeUrl; ?>/user/delete')">
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
                                            <th>用户名</th>
                                            <th>邮箱</th>
                                            <th>所属组</th>
                                            <th>操作</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="dataTables_empty">Loading data from server</td>
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
</div>

<div id="ajax-modal" class="modal hide fade" tabindex="-1"></div>