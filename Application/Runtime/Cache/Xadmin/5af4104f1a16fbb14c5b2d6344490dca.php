<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>代理列表-<?php echo (APP_NAME); ?></title>
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
        <h1 class="pagetitle">代理列表</h1>
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
                <th>代理昵称/微信号/手机号</th>
                <th>账户余额</th>
                <!-- <th>旗力币</th> -->
                <th>邀请码</th>
                <!-- <th>代理等级</th> -->
                <th>返佣比例</th>
                <th>上级(ID)</th>
                <th>下级代理</th>
                <th>玩家人数</th>
                <th>加入时间</th>
                <th>操作</th>
            </tr>

            <?php $cols = 10; ?>
            <?php if(empty($agentList)): ?><tr>
                    <td colspan="<?php echo ($cols); ?>">没有代理列表~！</td>
                </tr>
                <?php else: ?>
                <?php if(is_array($agentList)): $i = 0; $__LIST__ = $agentList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$agent): $mod = ($i % 2 );++$i;?><tr>
                        <td class="center"><?php echo ($agent['id']); ?></td>
                        <td class="center"><?php echo ($agent['nickname']); ?> / <?php echo ($agent['wechat_id']); ?> / <?php echo ($agent['phone']); ?></td>
                        <td class="center">
                            余额：<?php echo ($agent['available_balance']); ?>
                            <br>
                            累计：<?php echo ($agent['accumulated_income']); ?>
                        </td>
                        <!-- <td class="center"><?php echo ($agent['room_card']); ?></td> -->
                        <td class="center"><?php echo ($agent['invitation_code']); ?></td>
                        <!-- <td class="center"><?php echo ($agent['level_txt']); ?></td> -->
                        <td class="center">
                            平台返佣：<?php echo ($agent['rebateMoneyPercent1']); ?> %
                            <br>
                            下级返佣：<?php echo ($agent['rebateMoneyPercent2']); ?> %
                        </td>
                        <td class="center">
                        <?php if($agent['pid'] <= 0): ?>无
                            <?php else: ?>
                            <?php echo ($agent['p_info']['nickname']); ?>(<?php echo ($agent['pid']); ?>)<?php endif; ?>
                        </td>
                        <td class="center">
                                <?php echo ($agent['son_count']); ?>（人）
                        </td>
                        <td class="center">
                                <?php echo ($agent['player_count']); ?>（人）
                        </td>
                        <td class="center"><?php echo (time_format($agent['dateline'])); ?></td>
                        <td class="center">
                                <!-- <a class="stdbtn btn_lime" href="<?php echo U('Agent/addInsureScore', array('id'=>$agent['id']));?>">充卡</a>&nbsp;&nbsp; -->
                                <!-- <a class="stdbtn btn_lime" href="<?php echo U('Agent/erplayers', array('agentId'=>$agent['id']));?>">玩家列表</a>&nbsp;&nbsp; -->
                                <a class="stdbtn btn_lime" href="<?php echo U('Agent/agentSalesVolumeRecored', array('agentId'=>$agent['id']));?>">销售业绩</a>&nbsp;&nbsp;
                                <a class="stdbtn btn_lime resetPwd_confirm" action_href="<?php echo U('Agent/resetPwd', array('id'=>$agent['id']));?>">重置密码</a>&nbsp;&nbsp;
                                <a class="stdbtn btn_lime" href="<?php echo U('Agent/editAgent', array('id'=>$agent['id']));?>">编辑</a>&nbsp;&nbsp;
                                <a class="stdbtn btn_lime" href="<?php echo U('Agent/oneRebateConfig', array('id'=>$agent['id']));?>">返佣</a>&nbsp;&nbsp;
                                <!-- <?php if($agent['is_delete'] == 0): ?><a class="stdbtn btn_lime del_confirm" action_href="<?php echo U('Agent/delAgent', array('id'=>$agent['id']));?>">删除</a>&nbsp;&nbsp;
                                <?php else: ?>
                                    <a class="stdbtn btn_lime del_confirm" action_href="<?php echo U('Agent/recoveryAgent', array('id'=>$agent['id']));?>">恢复</a>&nbsp;&nbsp;<?php endif; ?> -->
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
            if(confirm('是否确定【'+action_name+'】?')){
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