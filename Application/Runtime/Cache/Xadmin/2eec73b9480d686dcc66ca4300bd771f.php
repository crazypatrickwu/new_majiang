<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>添加代理-<?php echo (APP_NAME); ?></title>
    <link rel="stylesheet" href="/Public/Admin/css/style.default.css" type="text/css" />
    <link rel="shortcut icon" href="/Uploads/favicon.ico" type="image/x-icon" />
    
</head>

<body class="withvernav">
    <div class="bodywrapper">
        <div class="topheader">
            <div class="left">
                <h1 class="logo">
                    <a href="<?php echo U('Index/statistics');?>" style="color:#0099cc;line-height: 60px;">
                        <!--<img src="/Public/Common/images/logo.png" height="40px" />-->
                        <?php echo (APP_NAME); ?>后台管理系统
                    </a>
                </h1>

                <ul class="right">
                    <li>欢迎你 <?php echo (session('adminAccount')); ?></li>
                </ul>
            </div>
        </div>
        
        <div class="header">
        	﻿<ul class="headermenu">
        <li class="<?php if(in_array(CONTROLLER_NAME, array('Index'))): ?>current<?php endif; ?>">
            <a href="<?php echo U('Xadmin/Index/statistics');?>">
                <span class="tet">控制台</span>
            </a>
            <em></em>
        </li>

        <?php if(is_array($admin_menu_list)): $i = 0; $__LIST__ = $admin_menu_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$top_menu): $mod = ($i % 2 );++$i;?><li class="<?php if(in_array(CONTROLLER_NAME, array($top_menu['m_controller']))): ?>current<?php endif; ?>">
                <a href="/Xadmin/<?php echo ($top_menu['name']); ?>">
                    <span class="tet"><?php echo ($top_menu['title']); ?></span>
                </a>
                <em></em>
            </li><?php endforeach; endif; else: echo "" ;endif; ?>

        <li>
            <a href="<?php echo U('Xadmin/Login/logout');?>">
                <span class="tet">退出登录</span>
            </a>
            <em></em>
        </li>
</ul>

        </div>
        <div class="main-date-lr">
            <div class="vernav2 iconmenu">
                
    <ul>
	<?php if(is_array($left_menu)): $i = 0; $__LIST__ = $left_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$big_menu): $mod = ($i % 2 );++$i;?><li>
			<a class="date-tit sys-tj" href="/Xadmin/<?php echo ($big_menu['big']['name']); ?>" class="addons"><?php echo ($big_menu['big']['title']); ?></a>
			<ul class="Jcon-ctr">
				<?php if(is_array($big_menu['son'])): $i = 0; $__LIST__ = $big_menu['son'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$son_menu): $mod = ($i % 2 );++$i;?><li<?php if(ACTION_NAME == $son_menu['m_action']): echo chr(32);?>class="on"<?php endif; ?>>
						<a href="/Xadmin/<?php echo ($son_menu['name']); ?>"><?php echo ($son_menu['title']); ?></a>
					</li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</li><?php endforeach; endif; else: echo "" ;endif; ?>
</ul>

                <a class="togglemenu"></a>
                <br /><br />
            </div>
            <div class="centercontent">
                
    <div class="pageheader">
        <h1 class="pagetitle">添加代理</h1>
        <span class="pagedesc"></span>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <form class="stdform stdform2" action="<?php echo U('Agent/addAgent');?>" method="post">
            <div class="line-dete">
                <label>新代理昵称</label>
                <span class="field">
                    <input type="text" id="nickname" name="nickname" class="smallinput" />
                </span>
            </div>
            <div class="line-dete">
                <label>新代理手机号</label>
                <span class="field">
                    <input type="text" id="phone" name="phone" class="smallinput"  />
                </span>
            </div>
            <div class="line-dete">
                <label>新代理微信号</label>
                <span class="field">
                    <input type="text" id="wechat_id" name="wechat_id" class="smallinput" />
                </span>
            </div>

            <div class="line-dete">
                <label>是否锁定</label>
                <span class="field">
                    <input type="radio" name="is_lock" value="1" />是
                    <input type="radio" name="is_lock" value="0" checked="checked" />否
                </span>
            </div>
            <div class="line-dete">
                <label></label>
                <span class="field">
                    <input type="hidden" name="id" value="0" />
                    <input type="submit" class="big-btn stdbtn" value="保存" />
                    <input type="button" class="big-btn stdbtn" onclick="window.history.back(-1);" value="返回" />
                </span>
            </div>
        </form>
    </div>

            </div>
        </div>
    </div>
    
    <script type="text/javascript" src="/Public/Admin/js/plugins/jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/plugins/fxw-base.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/plugins/pop_window.js"></script>
    <script type="text/javascript">
        function msgBox(title, content, time) {
            var _title = title ? title : '提示';
            var _time = time ? time : 1500;

            var html='<div class="title">' + _title + '</div><div>' + content + '</div>';
            popbox(html);
            setTimeout(function() {
                window.location.reload();
            }, _time);
        }
        $(".togglemenu").click(function(){
            $(".Jcon-ctr").toggle();
        });
    </script>
    
    <script type="text/javascript" src="/Public/Admin/js/ajaxfileupload.js"></script>
    <script type="text/javascript">
                        var getZoneAddress = "<?php echo U('Agent/getChildZone');?>";
                        var zoneData = [];
                        //初始化
                        $("#province").change(function() {
                            var id = $(this).find("option:selected").attr('data-id')
                            var fullElement = $("#city");
                            if (zoneData[id] != undefined) {
                                fullData(zoneData[id], fullElement);
                            } else {
                                $.ajax({
                                    url: getZoneAddress,
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {pid: id}
                                }).done(function(data) {
                                    zoneData[id] = data;
                                    fullData(zoneData[id], fullElement);
                                });
                            }
                        });

                        // 填充数据
                        function fullData(data, element, name) {
                            var string = [];
                            for (var i in data) {
                                string.push('<option data-id="' + data[i].id + '" value="' + data[i].region_name + '">' + data[i].region_name + '</option>');
                            }
                            element.html(string.join(''));
                            element.trigger("change");
                        }

                        // 省列表改变
                        $('.ip-shop-dizi').on('change', "#province", function() {
                            var id = $(this).find("option:selected").attr('data-id')
                            var fullElement = $("#city");

                            if (zoneData[id] != undefined) {
                                fullData(zoneData[id], fullElement);
                            } else {
                                $.ajax({
                                    url: getZoneAddress,
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {pid: id}
                                }).done(function(data) {
                                    zoneData[id] = data;
                                    fullData(zoneData[id], fullElement);
                                });
                            }
                        });

                        // 市列表改变
                        $('.ip-shop-dizi').on('change', "#city", function() {
                            var id = $(this).find("option:selected").attr('data-id');
                            var fullElement = $("#county");

                            if (zoneData[id] != undefined) {
                                fullData(zoneData[id], fullElement);
                            } else {
                                $.ajax({
                                    url: getZoneAddress,
                                    type: 'POST',
                                    dataType: 'json',
                                    data: {pid: id}
                                }).done(function(data) {
                                    if (data.length <= 0) {
                                        fullElement.remove();
                                    } else {
                                        zoneData[id] = data;
                                        fullData(zoneData[id], fullElement);
                                    }
                                });
                            }
                        });

                        $("#province").trigger('change');

    </script>

</body>
</html>