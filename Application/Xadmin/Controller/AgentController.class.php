<?php
/**
 * Created by MY_MIND.
 * User: WDJ
 * Date: 2018/3/28 0028
 * Time: 11:33
 */
namespace Xadmin\Controller;

use Common\Model\AgentModel;

class AgentController extends BaseController
{
    /**
     * 代理列表
     */
    public function agentList()
    {
        $model  =   new AgentModel();
        list($agentList , $show) = $model->xdminGetAgentList();
        $this->assign(['agentList'  =>  $agentList , 'show' =>  $show]);
        $this->display();
    }

    /**
     * 添加代理
     */
    public function addAgent()
    {
        if( IS_POST ) {
            $model  =   new AgentModel();
            list($flag , $message)  =   $model->xadminAddAgent();
            if($flag){
                $this->success($message , U('Agent/agentlist'));
            }else{
                $this->error($message , U('Agent/addAgent'));
            }
        } else {
            $region     = M('region');
            // 省
            $province   = $region->where(array('pid'=>1))->select();

            $this->assign('province', $province);
            $this->display('addAgent');
        }
    }

    /**
     * 编辑代理
     */
    public function editAgent()
    {
        if(IS_POST){
            $model  =   new AgentModel();
            list($flag , $message)  =   $model->xadminEditAgent();
            if($flag){
                $this->success($message , U('Agent/agentList'));
            }else{
                $this->error($message);
            }
        }else{
            $region     =   M('region');
            $province   =   $region->where(array('pid'=>1))->select();
            $agentInfo  =   AgentModel::agentInfo(intval(I('get.id')));

            $this->assign('province', $province);
            $this->assign('agentInfo', $agentInfo);
            $this->display();
        }
    }

    /**
     * 重置密码
     */
    public function resetPwd()
    {
        $model  =   new AgentModel();
        list($flag , $message)  =   $model->xadminResetPwd();
        if($flag){
            $this->success($message);
        }else{
            $this->error($message);
        }
    }

    /**
     * 返佣
     */
    public function oneRebateConfig()
    {
        $model  =   new AgentModel();
        if(IS_POST){
            list($flag , $message)  =   $model->xadminOneRebateConfig();
            if($flag){
                $this->success($message , U('Agent/agentList'));
            }else{
                $this->error($message);
            }
        }else{
            //首先判断是否一级代理
            $agent_info =   AgentModel::agentInfo(I('get.id' , '' , 'intval'));
            if($agent_info['level'] != 1){
                $this->error('平台只能设置一级代理返佣比例');
            }
            list($agentInfo , $agent_one_rebate_config) =   $model->xadminRebateConfig();
            $this->assign('agentInfo' , $agentInfo);
            $this->assign('agent_one_rebate_config' , $agent_one_rebate_config);
            $this->display();
        }
    }

    /**
     * 代理提现
     */
    public function incomeReport()
    {
        $model  =   new AgentModel();
        list($agentList , $show)    =   $model->xadminIncomeReport();
        $this->assign('agentList', $agentList);
        $this->assign('show', $show);
        $this->display('incomeReport');
    }

    /**
     * 代理审核
     */
    public function agentExamine()
    {
        $model  =   new AgentModel();
        list($agentList , $show)  =   $model->xadminAgentExamine();
        $this->assign('agentList', $agentList);
        $this->assign('show', $show);
        $this->display();
    }
}