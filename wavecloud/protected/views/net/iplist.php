<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3>网络<?=$netarr['net_name']?>：IP使用情况</h3>
</div>
<div class="modal-body">
    <table class="table table-bordered table-striped table-hover" id="tabledata">
        <thead>
            <tr>
                <th style="width: 40px;">ID</th>
                <th>外网IP</th>
                <th>内网IP</th>
                <th>使用状态</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($arr as $key => $value):?>
            <tr>
                <td><?=$value['id']?></td>
                <td><?=$value['ip']?></td>
                <td><?=$value['net_private']?></td>
                <td><?=$value['use_status']?></td>
            </tr>
            <?php endforeach;?>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn btn-primary">确定</button>
</div>