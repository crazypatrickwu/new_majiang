<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>代理提现-<?php echo (APP_NAME); ?></title>
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
        <h1 class="pagetitle">代理提现</h1>
        <span class="pagedesc"></span>
    </div>
    <div id="contentwrapper" class="contentwrapper">
        <div>
            <form action="" metod="get">
                &nbsp;
                代理昵称&nbsp;:&nbsp;<input type="text" name="nickname" id="nickname" value="<?php echo remove_xss(I('get.nickname'));?>">
                &nbsp;
                手机号码&nbsp;:&nbsp;<input type="text" name="phone" id="phone" value="<?php echo remove_xss(I('get.phone'));?>">
                &nbsp;
                加入时间&nbsp;:&nbsp;
                <input type="text" name="start_time" id="start_time" value="<?php echo remove_xss(search_time_format(I('get.start_time')));?>" class="sang_Calender">
                -
                <input type="text" name="end_time" id="end_time" value="<?php echo remove_xss(search_time_format(I('get.end_time')));?>" class="sang_Calender">
                &nbsp;&nbsp;
                <input type="submit" value="查询" class="stdbtn">
                <!-- <input type="submit" name="daochu" value="导出" class="stdbtn"> -->
                <p></p>
                <p></p>
            </form>
        </div>
        <p></p>
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable stdtablequick">
            <tr>
                <th width="3%">ID</th>
                <th>代理昵称/微信/手机号</th>
                <th>姓名/支付宝/银行卡/支行名称</th>
                <th>提现金额</th>
                <th>申请时间</th>
                <th>状态</th>
                <th>操作</th>
            </tr>
            <?php $cols = 7; ?>
            <?php if(empty($agentList)): ?><tr>
                    <td colspan="<?php echo ($cols); ?>">没有代理列表~！</td>
                </tr>
                <?php else: ?>
                <?php if(is_array($agentList)): $i = 0; $__LIST__ = $agentList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$agent): $mod = ($i % 2 );++$i;?><tr>
                        <td class="center"><?php echo ($agent['id']); ?></td>
                        <td class="center">
                            <?php echo ($agent['nickname']); ?> / <?php echo ($agent['wechat_id']); ?> / <?php echo ($agent['phone']); ?>
                        </td>
                        <td class="center">
                            <?php echo ((isset($agent['truename']) && ($agent['truename'] !== ""))?($agent['truename']):'未知'); ?> / <?php echo ((isset($agent['alipay_account']) && ($agent['alipay_account'] !== ""))?($agent['alipay_account']):'未知'); ?> / <?php echo ($agent['bank_name']); ?>：<?php echo ((isset($agent['bank_card']) && ($agent['bank_card'] !== ""))?($agent['bank_card']):'未知'); ?>/<?php echo ((isset($agent['bank_subbranch']) && ($agent['bank_subbranch'] !== ""))?($agent['bank_subbranch']):'未知'); ?>
                        </td>
                        <td class="center"><?php echo ($agent['money']); ?></td>
                        <td class="center"><?php echo (time_format($agent['dateline'])); ?></td>
                        <td class="center">
                        <?php if($agent['status'] == 1): ?><font color="green">已处理√</font>
                        <?php elseif($agent['status'] == 2): ?>
                            <font color="grey">已驳回×</font>
                        <?php else: ?>
                            <font color="red">未处理☆</font><?php endif; ?>
                        </td>
                        <td class="center">
                                <?php if($agent['status'] != 0): ?>--
                                <?php else: ?>
                                    <a class="stdbtn btn_lime" title="标记为已处理" href="<?php echo U('Agent/incomeReportHandle', array('id'=>$agent['id']));?>">处理</a>&nbsp;&nbsp;<?php endif; ?>
                        </td>
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
        //单个删除
        $(".del_confirm").on('click',function(){
            var action_name = $(this).html();
            var action_url = $(this).attr("action_href");
            //                        alert("action_name:"+action_name+";action_url:"+action_url);return;
            if(confirm('是否确定【'+action_name+'】当前信息?')){
                location.href = action_url;
            }
        });
        
        //密码重置
        $(".resetPwd_confirm").on('click',function(){
            var action_name = $(this).html();
            var action_url = $(this).attr("action_href");
            //                        alert("action_name:"+action_name+";action_url:"+action_url);return;
            if(confirm('是否确定【'+action_name+'】?')){
                location.href = action_url;
            }
        });
    </script>

</body>
</html>