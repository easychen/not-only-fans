import React, { Component,Fragment } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import { Button, Intent, Spinner } from "@blueprintjs/core";
import { toast , showApiError, inGroup } from '../util/Function';

import Web3 from 'web3';
import ABI from '../util/ABI';

@withRouter
@translate()
@inject("store")
@observer
export default class BuyVipButton extends Component
{
    state = {"checking":false};

    componentDidMount()
    {
       this.loadMetaMaskInfo(); 
    }

    // async loadMetaMaskInfo()
    // {
    //     const local_web3 = !window.web3 ? false : new Web3( window.web3.currentProvider );

    //     this.setState( { "web3":local_web3 } );
    // }
    async loadMetaMaskInfo()
    {   
        this.web3 = new Web3(Web3.givenProvider); 
        this.web3.transactionConfirmationBlocks=1;  
    }
    
    async buy()
    {
        const { group , t } = this.props;

        if( !this.web3 || typeof window.ethereum == 'undefined' )
        {
            toast(t("MetaMask 插件没有正确安装，请重装后刷新页面继续"));
            return false;
        }

        let default_account = this.web3.eth.defaultAccount;
        if( !default_account )
        {
            toast(t("正在连接到MetaMask"));
            // connect to metamask
            const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
            default_account = accounts[0];
        }

        const LianMiContract = new this.web3.eth.Contract( ABI , this.props.store.lianmi_contract );

        if( parseInt( group.id , 10 ) < 1 )
        {
            toast(t("错误的栏目ID，请刷新页面后重试"));
            return  false;
        }

        if( parseInt( this.props.store.user.id , 10 ) < 1 )
        {
            toast(t("错误的用户ID，请退出重新登入后再试"));
            return  false;
        }

        // toast( this.props.store.user.id  );
        LianMiContract.methods.buyGroupMembership( group.id , this.props.store.user.id ).send({"from":default_account,"value":group.price_wei}).once('receipt',async( receipt )=>{
            console.log("receipt");
            // 交易成功
            toast( t("交易已经成功，我们将在几分钟内为您添加对应的权限") );
            this.setState({"checking":true});
            
            const { data } = await this.props.store.checkVipContract( group.id );

            this.setState({"checking":false});

            if( !showApiError( data , t )  )
            {
                const expire = data.data.vip_expire ? t('，您的VIP订户到期时间为 ') + data.data.vip_expire  : '';
                toast( t("更新完成") + expire  );
                
                // 更新 store 里边的用户信息
                this.props.store.updateUserInfo();

            }  
            
            
            // alert("hash"+this.hash);

        })
        .on('confirmation', function(confirmationNumber, receipt){
            console.log( confirmationNumber, receipt );
        })
        .once('transactionHash', ( hash ) =>
        {
            console.log("transactionHash", hash);
            // 交易提交
            this.hash = hash;
            toast( t("交易已经提交，请等待区块链网络进行处理。整个过程将持续10秒到几分钟不等") );
            this.setState({"checking":true});
        } )
        .once('error',(info) => 
        {
            console.log("error", info);

            this.setState({"checking":false});
            if( this.hash )
            {
                toast( t("交易发生了一些问题，我们将为您打开详细的交易页面") );
                window.open("https://ropsten.etherscan.io/tx/"+this.hash);
            }
            else
            {
                toast( t("交易被取消") );
            }
            
        });
        
    }
    
    render()
    {
        const { user } = this.props.store;
        const { group , t } = this.props;
        const is_vip = user.vip_groups && inGroup( group.id , user.vip_groups ) ? true : false;

        return <div className="flexcol"><div className="buy vcenter">
        { is_vip && <Button text={this.props.renewaltext} className={this.props.className}  onClick={()=>this.buy( group.id )}  disabled={this.state.checking} icon="endorsed"  />
        }

        { !is_vip && <Button onClick={()=>this.buy( group.id )} disabled={this.state.checking} text={this.props.text} className={this.props.className} />
        }

    </div>
    <div className="hcenter p-5">
    { this.state.checking && <Spinner intent={Intent.PRIMARY} small={true}/>}
    </div>
    </div> ;
    }
}