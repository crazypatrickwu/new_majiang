<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>代理销售额-<?php echo (APP_NAME); ?></title>
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
        <h1 class="pagetitle">代理销售额</h1>
        <span class="pagedesc"></span>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <div>
            <form action="" metod="get">
                玩家ID&nbsp;:&nbsp;<input type="text" name="userid" id="userid" value="<?php echo remove_xss(I('get.userid'));?>">
                &nbsp;
                时间&nbsp;:&nbsp;<input type="text" name="start_time" id="start_time" value="<?php echo remove_xss(search_time_format(I('get.start_time')));?>" class="sang_Calender">-<input type="text" name="end_time" id="end_time" value="<?php echo remove_xss(search_time_format(I('get.end_time')));?>" class="sang_Calender">
                <input type="hidden" name="p" value="1" class="stdbtn">&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="submit" value="查询" class="stdbtn">&nbsp;&nbsp;&nbsp;&nbsp;
                <!-- <input type="button" value="重置" class="stdbtn" id="resetForm">&nbsp;&nbsp;&nbsp;&nbsp; -->
                <!-- <input type="button"  value="数据导出" class="stdbtn" id="export"> -->
                 &nbsp;
                 总计：<font color='red' size='6'><?php echo ($agent_sales_volume_total); ?></font><font size='4'>&nbsp;元</font>
                <p></p>
                <p></p>
            </form>
        </div>
        <p></p>
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
            <tr>
                <th width="20%">销售编号</th>
                <th width="30%">玩家ID</th>
                <th width="30%">销售额</th>
                <th>消费时间</th>
            </tr>
            <?php $cols=4; ?>
            <?php if(empty($agent_sales_volume_recored)): ?><tr>
                    <td colspan="<?php echo ($cols); ?>">没有销售列表~！</td>
                </tr>
                <?php else: ?>
                <?php if(is_array($agent_sales_volume_recored)): $i = 0; $__LIST__ = $agent_sales_volume_recored;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$recored): $mod = ($i % 2 );++$i;?><tr>
                        <td class="center"><?php echo ($recored['id']); ?></td>
                        <td class="center"><?php echo ($recored['uid']); ?></td>
                        <td class="center"><?php echo ($recored['total_price']); ?>（元）</td>
                        <td class="center"><?php echo (time_format($recored['dateline'])); ?></td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                <tr>
                    <td colspan="<?php echo ($cols); ?>">
                        <div class="page-box"><?php echo ($show); ?></div>
                    </td>
                </tr><?php endif; ?>
            </tbody>
        </table>
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
    
    <script type="text/javascript" src="/Public/Admin/js/datetime.js"></script>
    <script type="text/javascript">
    //点击导出按钮，执行相关操作
    $("#export").click(function(){
        window.location.href="<?php echo U('Recharge/playersChargeRecored',array('type'=>$line_type,'export_type'=>$line_type));?>";    
    });

    $("#resetForm").click(function(){
        window.location.href="<?php echo U('Recharge/playersChargeRecored');?>";    
    });

    </script>

</body>
</html>