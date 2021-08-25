import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import { toast , showApiError } from '../util/Function';
import { Button, FormGroup, Intent, InputGroup, Callout, Spinner } from '@blueprintjs/core';

// import Web3 from 'web3';
import ABI from '../util/ABI';

import Cloumn3Layout from '../component/Cloumn3Layout';
import UserCard from '../component/UserCard';

@translate()
@inject("store")
@withRouter
@observer
export default class GroupPay extends Component
{
    state = {"group":{},"checking":false};

    componentDidMount()
    {
       this.loadGroupInfo(); 
       this.loadMetaMaskInfo(); 
    }

    // async loadMetaMaskInfo()
    // {
    //     const local_web3 = !window.web3 ? false : new Web3( window.web3.currentProvider );

    //     this.setState( { "web3":local_web3 } );
    // }
    async loadMetaMaskInfo()
    {   
        /*eslint-disable no-undef*/
        if (typeof web3 !== 'undefined') 
        {

            console.log( web3 );
            this.setState( { "web3":new Web3( web3.currentProvider ) } );
        } 
        /*eslint-enable no-undef*/        
    }

    async loadGroupInfo()
    {
        const { t } = this.props;
        const gid = this.props.match.params.id;
        
        if( parseInt( gid , 10  )> 0 )
        {
            const { data } = await this.props.store.getGroupDetail( gid );
            console.log( data );
            if( !showApiError( data , t )  )
            {
                //console.log( data );
                this.setState( { "group":data.data } );
            }   
        }
    }

    // async pay()
    // {
    //     const { t } = this.props;

    //     if( !this.state.web3 )
    //     {
    //         toast(t("MetaMask 插件没有正确安装，请重装后刷新页面继续"));
    //         return false;
    //     }
        
    //     if( !window.web3.eth.defaultAccount || !Web3.utils.isAddress( window.web3.eth.defaultAccount ) )
    //     {
    //         toast(t("请点击浏览器中的 MetaMask 图标，并在弹出的页面中输入密码解锁后继续"));
    //         return false;
    //     }
        
    //     const LianMiContract = new this.state.web3.eth.Contract( ABI , this.props.store.lianmi_contract );

    //     if( parseInt( this.state.group.id , 10 ) < 1 )
    //     {
    //         toast(t("错误的栏目ID，请刷新页面后重试"));
    //         return  false;
    //     }

    //     if( parseInt( this.props.store.user.id , 10 ) < 1 )
    //     {
    //         toast(t("错误的用户ID，请退出重新登入后再试"));
    //         return  false;
    //     }

    //     // toast( this.props.store.user.id  );
    //     LianMiContract.methods.createGroup( this.state.group.id ).send({"from":window.web3.eth.defaultAccount,"value":Web3.utils.toWei( '0.001' , 'ether' )}).once('receipt',async( receipt )=>{
    //         // 交易成功
    //         toast( t("交易已经成功，我们将在几分钟内完成栏目的创建工作") );
    //         this.setState({"checking":true});
            
    //         const { data } = await this.props.store.checkGroupContract( this.state.group.id );

    //         this.setState({"checking":false});

    //         if( !showApiError( data , t )  )
    //         {
    //             await this.props.store.updateUserInfo();
    //             toast( t("栏目已成功开通") );
    //             this.props.history.push( '/group/' + this.state.group.id );
    //         }  
            
            
    //         // alert("hash"+this.hash);

    //     }).once('transactionHash', ( hash ) =>
    //     {
    //         // 交易提交
    //         this.hash = hash;
    //         toast( t("交易已经提交，请等待区块链网络进行处理。整个过程将持续10秒到几分钟不等") );
    //         this.setState({"checking":true});
    //     } )
    //     .once('error',(info) => 
    //     {
    //         this.setState({"checking":false});
    //         toast( t("交易发生了一些问题，我们将为您打开详细的交易页面") );
    //         window.open("https://ropsten.etherscan.io/tx/"+this.hash);
    //     });

        


    // }
    
    render()
    {
        const { t } = this.props;
        const main=<div className="blocklist">
        <div className="groupform">
            <form className="px10list">
                <h1 className="lianmi">{t("支付栏目创建的区块链写入费用")}</h1>
                <div className="groupdetail px10list">
                    <img src={this.state.group.cover} alt=""/>
                    <div className="name">{this.state.group.name}</div>
                </div>
                
                {!this.state.web3 && <div><Callout intent={Intent.WARNING}>
                    {t("为了能在浏览器上进行支付，您需要安装浏览器钱包插件 MetaMask，然后向钱包中转入少量ETH后支付。请按提示安装后点击按钮继续。（亦可直接导入私钥，但转账的方式更为安全。）")}
                </Callout>
                <div className="guidebar top10">
                    <Button intent={Intent.PRIMARY} text={t("已安装完成，点此继续")} large={true} onClick={()=>window.location.reload()}/>

                    <Button text={t("查看 MetaMask 安装教程")} large={true} onClick={()=>window.open(t("http://lianmi.io/HOW-TO-INSTLL-MEATMASK"))}/>
                </div>
                </div>
                }

                {this.state.web3 && <div><Callout intent={Intent.PRIMARY}>
                    {t("您的栏目将采用区块链智能合约进行分账，为了将相关信息写入区块链，并同步到全网络成千上万个节点，需要支付一定的费用。PS：测试阶段使用 Ropsten 测试网络，可到 faucet.metamask.io 领取ETH，切勿使用主网支付")}
                </Callout>
                <Button intent={Intent.PRIMARY} text={t("支付 0.001 ETH")} large={true} onClick={()=>this.pay()} disabled={this.state.checking} className="top10"/>
                
                </div>
                }

                {this.state.checking && <div className="checking">
                <Spinner intent={Intent.PRIMARY} large={true}/>
                <p className="top10">{t("正在和区块链网络进行通信，整个过程将与数十个节点进行交互，可能花费几分钟时间，请耐心等待")}</p>
                </div>}
            </form>
            </div></div>    ;
        return <Cloumn3Layout left={<UserCard/>} main={main} />;
    }
}