import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter } from 'react-router-dom';
import { translate, Trans } from 'react-i18next';
import { Button, Intent, Spinner, Overlay, Icon } from "@blueprintjs/core";
import { toast , showApiError, inGroup, isApiOk } from '../util/Function';
import QRImage from '../component/QRImage';
import { sprintf } from 'sprintf-js';
// import FIBOS from 'fibos.js'

// import Web3 from 'web3';
// import ABI from '../util/ABI';

@withRouter
@translate()
@inject("store")
@observer
export default class BuyVipButton extends Component
{
    state = {"checking":false,"qr_url":false,"schema":false};

    constructor(props) 
    {
        super(props);
        this.btnRef = React.createRef();
    }
    
    componentDidMount()
    {
       // this.loadMetaMaskInfo(); 
    }

    // async loadMetaMaskInfo()
    // {
    //     const local_web3 = !window.web3 ? false : new Web3( window.web3.currentProvider );

    //     this.setState( { "web3":local_web3 } );
    // }

    async checkOrder( order_id )
    {
        this.btnRef.current.buttonRef.disabled = true;
        const { data } = await this.props.store.checkOrder( order_id );
        this.btnRef.current.buttonRef.disabled = false;
        
        if( isApiOk( data ) )
        {
            if( data.data.done == 1 )
            {
                toast("支付完成，将刷新页面状态");
                await this.props.store.updateUserInfo();
                window.location.reload();
            }
        }
        else showApiError( data , this.props.t );
    }
    
    async buy()
    {
        // // 测试浏览器支付流程
        // const fibos_client = FIBOS({'chainId': '6aa7bd33b6b45192465afa3553dedb531acaaff8928cf64b70bd4c5e49b7ec6a',
        // 'httpEndpoint': 'https://to-rpc.fibos.io',
        // 'keyProvider': ''});

        // // console.log(  fibos_client.getInfoSync() );

        // fibos_client.transfer('phpisthebest', 'phpisthetest', '1 FO', 'order id').then(r=>{
        //     console.log(r);
        // });

        
        // return ;

        const { group , t } = this.props;

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

        // 首先在数据库中插入一条预付费记录
        const { data } = await this.props.store.preorder( group.id );
        if( data.data  )
        {
            const url = data.data.url;
            const order_id = data.data.order_id;

            if( window.fowallet )
            {
                // 显示 schema 连接
                this.setState( {"schema":data.data.schema,"order_id":order_id} );
            }
            else
            {
                // 显示二维码
                this.setState( {"qr_url":url,"order_id":order_id} );
            }
            

            // 然后将记录id作为备注生成二维码，展示给当前用户
            // $url = "https://wallet.fo/Pay?params=phpisthetest,FOUSDT,eosio,0.01,".u("order=".data.data);

            // FOUSDT@eosio
        }

        // 当用户通过手机APP支付完成以后 

        // 




        // if( !this.state.web3 )
        // {
        //     toast(t("MetaMask 插件没有正确安装，请重装后刷新页面继续"));
        //     return false;
        // }
        
        // if( !window.web3.eth.defaultAccount || !Web3.utils.isAddress( window.web3.eth.defaultAccount ) )
        // {
        //     toast(t("请点击浏览器中的 MetaMask 图标，并在弹出的页面中输入密码解锁后继续"));
        //     return false;
        // }
        
        // const LianMiContract = new this.state.web3.eth.Contract( ABI , this.props.store.lianmi_contract );

        // if( parseInt( group.id , 10 ) < 1 )
        // {
        //     toast(t("错误的栏目ID，请刷新页面后重试"));
        //     return  false;
        // }

        // if( parseInt( this.props.store.user.id , 10 ) < 1 )
        // {
        //     toast(t("错误的用户ID，请退出重新登入后再试"));
        //     return  false;
        // }

        // // toast( this.props.store.user.id  );
        // LianMiContract.methods.buyGroupMembership( group.id , this.props.store.user.id ).send({"from":window.web3.eth.defaultAccount,"value":group.price_wei}).once('receipt',async( receipt )=>{
        //     // 交易成功
        //     toast( t("交易已经成功，我们将在几分钟内为您添加对应的权限") );
        //     this.setState({"checking":true});
            
        //     const { data } = await this.props.store.checkVipContract( group.id );

        //     this.setState({"checking":false});

        //     if( !showApiError( data , t )  )
        //     {
        //         const expire = data.data.vip_expire ? '，您的VIP订户到期时间为：'+ data.data.vip_expire  : '';
        //         toast( t("支付成功" + expire + '。' ) );
                
        //         // 更新 store 里边的用户信息
        //         this.props.store.updateUserInfo();

        //     }  
            
            
        //     // alert("hash"+this.hash);

        // }).once('transactionHash', ( hash ) =>
        // {
        //     // 交易提交
        //     this.hash = hash;
        //     toast( t("交易已经提交，请等待区块链网络进行处理。整个过程将持续10秒到几分钟不等") );
        //     this.setState({"checking":true});
        // } )
        // .once('error',(info) => 
        // {
        //     this.setState({"checking":false});
        //     if( this.hash )
        //     {
        //         toast( t("交易发生了一些问题，我们将为您打开详细的交易页面") );
        //         window.open("https://ropsten.etherscan.io/tx/"+this.hash);
        //     }
        //     else
        //     {
        //         toast( t("交易被取消") );
        //     }
            
        // });
        
    }
    
    render()
    {
        const { user } = this.props.store;
        const { group , t } = this.props;
        const is_vip = user.vip_groups && inGroup( group.id , user.vip_groups ) ? true : false;

        return <div className="buy vcenter">
        { is_vip && <Button text={this.props.renewaltext} className={this.props.className}  onClick={()=>this.buy( group.id )}  disabled={this.state.checking} icon="endorsed"  />
        }

        { !is_vip && <Button onClick={()=>this.buy( group.id )} disabled={this.state.checking} text={this.props.text} className={this.props.className} />
        }

        { this.state.checking && <Spinner intent={Intent.PRIMARY} small={true}/>}

        <Overlay isOpen={this.state.qr_url}>
        
        <div className="overbox">
        <div className="section-qr">
            <div className="close-button" onClick={()=>this.setState({"qr_url":false})}><Icon icon="cross" iconSize={20} /></div>
            
            <QRImage value={this.state.qr_url} className="qrimg-box"  /> 
            <div className="explain">
            <Trans i18nKey="UseFowallet">
                请用<a href="https://wallet.fo/zh-cn/" target="_blank" rel="noopener noreferrer" >FO钱包</a>扫码转账</Trans>
            </div>
            <div className="goon">
                <Button ref={this.btnRef} text={this.props.t("转账完成后点我继续")} onClick={()=>this.checkOrder(this.state.order_id)} large={true} />
            </div>
            
        </div>
        </div>
        
        </Overlay>

        <Overlay isOpen={this.state.schema}>
        
        <div className="overbox">
        <div className="section-qr">
            <div className="close-button" onClick={()=>this.setState({"schema":false})}><Icon icon="cross" iconSize={20} /></div>
            
            
            <div className="explain">
            <Trans i18nKey="UseFowalletLink">
                请用<a href={this.state.schema} target="_blank" rel="noopener noreferrer">点这里</a>进入转账界面
            </Trans>    
            </div>
            <div className="goon">
                <Button text={t("转账完成后点我继续")} ref={this.btnRef} onClick={()=>this.checkOrder(this.state.order_id)} large={true} />
            </div>
            
        </div>
        </div>
        
        </Overlay>

    </div> ;
    }
}