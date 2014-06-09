'use strict';
if (!Array.prototype.every) {    
    Array.prototype.every = function(fun ) {    
        "use strict";    

        if (this === void 0 || this === null)    
                    throw new TypeError();    
        var t = Object(this);    
        var len = t.length >>> 0;    
        if (typeof fun !== "function")    
                     throw new TypeError();    
        var thisp = arguments[1];    
        for (var i = 0; i < len; i++) {    
                        if (i in t && !fun.call(thisp, t[i], i, t))    
                                return false;    
        }    

        return true;
    };    
}
var commonAjaxXhr = {};
function commonAjax(config){//发起ajax请求
    commonAjaxXhr[ config.url ] && commonAjaxXhr[ config.url ].abort();
    config = $.extend({
        type:'post',
        dataType:'json',
        data:config.datas
    },config);
    return commonAjaxXhr[ config.url ] = $.ajax(config);
}

function loadAjaxFn(status){//ajax加载loading
    var mask = $('#load-mask'),loadBox = $('#loading-box');
    mask.height(document.documentElement.scrollHeight)[status](function(){
        loadBox[status]();
    });
}

var commonWinuids = [];
function commonWin(arg) { //通用弹窗
    function initWin(){
        $('#' + arg.id).modal({ //初始化对话框
            backdrop: true,
            keyboard: true,
            show: true
        });
    }
    for(var i = 0, len = commonWinuids.length; i < len; i++) {
        if(commonWinuids[i] === arg.uid) {
            initWin();
            return;
        }
    }
    arg = $.extend(arg, {
        type: 'text',
        width: 400
    });
    var items = '',
    commonWinObj = $('<div class="modal hide fade " id="' + arg.id + '"></div>');
    items += '<div class="modal-header">' + '<a class="close" data-dismiss="modal">×</a>' + '<h3>' + arg.title + '</h3>' + '</div>';
    items += '<div class="modal-body">';
    if(arg.diyLayout){
        items += arg.diyLayout();
    }else{
        if( arg.errTips instanceof Array){
            items += '<div class="alert alert-error" style="display:none;" id="' + arg.errTips[0] + '">' +
                         '<div id="' + arg.errTips[1]+ '"></div>' +
            '</div>';
        }
        items += '<form class="form-horizontal"><fieldset>';
        $.each(arg.inputs, function(i, v) {
            items += '<div class="control-group"><label class="control-label" for="' + v.id + '">' + v.label + '</label>' + '<div class="controls"><input ' + (v.name ? 'name="' + v.name + '"' : '') + (v.value ? ' value="' + v.value + '"' : '') + ' type="' + (v.type || arg.type) + '" class="input-big" id="' + v.id + '" autofocus="autofocus" /></div></div>';
        });
        items += '</fieldset></form>';
    }

    items += '</div>';
    items += '<div class="modal-footer">';
    $.each(arg.buttons, function(i, v) {
        i == 0 ? items += '<a href="javascript:void(0)" class="btn btn-primary"' + (v.id ? 'id="' + v.id + '"' : '') + (v.disclose ? '' : ' data-dismiss="modal"') + '>' + v.text + '</a>' : items += '<a href="javascript:void(0)" class="btn"' + (v.id ? 'id="' + v.id + '"' : '') + ' data-dismiss="modal">' + v.text + '</a>';
    });
    items += '</div></div>';
    commonWinObj.html(items);
    $(document.body).append(commonWinObj);
    commonWinuids.push(arg.uid);
    initWin();
    return commonWinObj;
}

        
function msgAlert(settings){
    settings.buttons = settings.buttons || [{
        text: '确定',
        id: 'alert-btn'
    }, {
        text: '取消'
    }];
    commonWin({ //生成对话框
        uid: 9999,
        id: settings.id,
        title: settings.title,
        diyLayout: function(){
                return '<p>' + settings.msg + '</p>';
        },
        buttons: settings.buttons 
    });
    document.getElementById( settings.buttons[0].id ).onclick = function(){
        if ( 'function' == typeof settings.fn ) {settings.fn.call(location)}
    };
    $('#'+settings.id+' .modal-body p').html(settings.msg);
}

function dateFormat( date ){
    if ( date instanceof Date ) {
            var month = date.getMonth() + 1,days = date.getDate();
            month = month < 10 ? '0' + month : month;
            days = days < 10 ? '0' + days : days;
            return date.getFullYear() + '-' + month + '-' + days;
    }
    return null;
}

var warningShowBox = function(txt, dom){
    $("#"+dom).show();
    $("#"+dom).html(txt);
}
var warningHideBox = function(dom){
    $("#"+dom).hide();
}

var bootstrapData = {}, listData = function( args ) {
    var renderT = function( options ){
        options.uid = options.uid || +new Date;
        if( options.columns.length >>> 0){
            for( var i = 0, len = options.columns.length; i < len; i++ ){
                if( !jQuery.isPlainObject( options.columns[i] )){
                    options.columns[i] = {
                        mData : options.columns[i]
                    }
                }
            }
        }
        var paginate = false;
        var t = '';
        if (options.paginate) {
            paginate = true;
            t = "t<'row-fluid'<'span6'i><'span6'p>>";
        }
        options = $.extend({
                        "sDom": t,//t<'row-fluid'<'span6'i><'span6'p>>
                        "sPaginationType": "bootstrap",
                        "sAjaxSource": options.url,
                        "bSort": false,
                        "aoColumns": options.columns,
                        "bPaginate": paginate,
                        "bAutoWidth": true,
                        "bLengthChange": true,
                        "bFilter": false,
                        "bDestroy" : true,
                        "bInfo" : paginate,
                        "oLanguage": {
                             "sInfo": "当前显示 _START_ 到 _END_ 条，共 _TOTAL_ 条记录",
                                "oPaginate": {
                                    "sPrevious" : "上一页 ",
                                    "sNext"     : "下一页 ",
                            }
                        },
                        "bServerSide" : options.serverside,
                        "fnInitComplete":function(){
                                $( '#' + options.tableId ).css('width','100%');
                        }
                }, options);

        var targetFlag = options.tableId + options.url;
                bootstrapData[targetFlag] = $( '#' + options.tableId ).dataTable( options );
    }

    if( args.length >>> 0 ){
        for( var i = 0, len = args.length; i < len; i++ ){
            renderT( args[i] );
        } 
    }else if( jQuery.isPlainObject( args ) ){
        renderT( args );
    }
};

var errorTop = function(msg, fn) {
    msgAlert({
      title: '提示信息',
      id: 'msg-alert-wrap',
      msg: msg,
      buttons: [{
            text: '确定',
            id: 'alert-btn'
        }, {
            text: '取消'
        }],
      fn: function() {
        if ('function' === typeof fn) {
          return fn();
        }
      }
    });
};

/**
 * 全选/全不选
 */
var checkboxAll = function(the){
    var checked = the.checked;
    if(checked == true){
        $("input[name='checkbox']").attr("checked", 'true');
        $(".btnchose").removeClass('disabled');
    }else{
        $("input[name='checkbox']").removeAttr("checked");
        $(".btnchose").addClass('disabled');
    }
}

var checkboxStatus = function(){
    var ids = "";
    $("input[name='checkbox']").each(function(){
        if(this.checked == true){
            ids += $(this).val() + ',';
            $(this).attr("checked", 'true');
        }
    })

    if(ids == "")
        $(".btnchose").addClass('disabled');
    else
        $(".btnchose").removeClass('disabled');

    return ids;
}

/**
 * check
 */
var checkBox = function(the){
    if(the.checked == true){
        $(".btnchose").removeClass('disabled');
    }else{
        checkboxStatus();
    }
}

/**
 * 删除方法
 */
var deleteFunc = function(durl){
    var disabled = $("#delete").hasClass("disabled");
    if(!disabled){
        errorTop( '确定删除？', function() {
            loadAjaxFn('fadeIn');
            var ids = checkboxStatus();
            $.ajax({
                type: "POST",
                url: durl,
                dataType: "JSON",
                data: "ids="+ids,
                success: function(data){
                    if ( data.success ) {
                        loadAjaxFn('fadeOut');
                        alert(data.msg);
                        fetchData();
                        $("#delete").addClass('disabled');
                    }else{
                        alert( data.msg );
                    }
                }
            });
        });
    }
}

/**
 * 编辑
 */
var editFunc = function(url){
    var $modal = $('#ajax-modal');
    $('body').modalmanager('loading');
    setTimeout(function(){
        $modal.load(url, '', function(){
            $modal.modal();
        });
    }, 50);
}



