import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import { AnchorButton, FormGroup, Slider, Intent, InputGroup, Callout } from '@blueprintjs/core';
import Dropzone from 'react-dropzone';
import ReactAvatarEditor from 'react-avatar-editor';
import { toast , showApiError, is_fo_address } from '../util/Function';
import { sprintf } from 'sprintf-js';

import Web3 from 'web3';

import Cloumn3Layout from '../component/Cloumn3Layout';
import UserCard from '../component/UserCard';

@translate()
@inject("store")
@withRouter
@observer
export default class GroupCreate extends Component
{
    state = {"name":"","image":"/cover.png","scale":1,"cover":"","price":0.01,"address":this.props.store.user.address};
    // https://ws1.sinaimg.cn/large/40dfde6fgy1fsq5bcd794j20da0dq0th.jpg

    componentDidMount()
    {
       if( !Web3.givenProvider )
       {
           toast(this.props.t("请先安装MetaMask等插件"));
       }else
       {
        this.web3 = new Web3(Web3.givenProvider);
       }   
    }

    changed( e , name )
    {
        let o = {};
        o[name] =  e.target.value ;
        this.setState( o ) ;
    }

    handleDrop = dropped => 
    {
        this.setState({ image:dropped[0] });
    }

    create(e)
    {
        const { t } = this.props;

        // 检查本地数据的完整性
        
        if( this.state.name.length < 1 )
        {
            toast(t("栏目名称不能为空"));
            return false;
        }
        // 检查提现地址，这个很重要
        // if( this.state.address.length < 1 || !is_fo_address( this.state.address  ) )
        // {
        //     toast(t("收费账户为空或者格式不正确，请按正确的格式填写"));
        //     return false;
        // }
        if( this.state.address.length < 1 || !this.web3.utils.isAddress( this.state.address  ) )
        {
            toast(t("提现地址为空或者格式不正确，请填写ETH（以太坊）用钱包地址"));
            return false;
        }
        
        // 检查定价
        const price_wei = this.web3.utils.toWei( this.state.price + '' , 'ether' )
        if( price_wei <= 0  )
        {
            toast(t("年费定价不能为0"));
            return false;
        }

        

        // 开始上传封面图片
        
        this.editor.getImageScaledToCanvas().toBlob( async (blob) => 
        {
            const { data } = await this.props.store.uploadCover( blob );
            if( !showApiError( data , t ) )
            {
                // 封面图片上传完成，地址为 data.data.url
                
                if( !data.data.url || data.data.url.length < 1 )
                {
                    toast(t("图片上传失败，请重试"));
                    return false;
                }

                const cover = data.data.url;
                // price_wei
                // 调用 store 的 createGroup 方法 
                const ret = await this.props.store.createGroup( this.state.name , price_wei , this.state.address , cover  );

            
                if( !showApiError( ret.data , t ) )
                {
                    const data2 = ret.data.data;
                    const groupname = data2.name ? data2.name : '';
                    const groupid = data2.id ? data2.id : 0 ;
                    
                    
                    toast( sprintf( t('栏目·%s[No.%s]创建成功。') , groupname, groupid ) )
                    // toast( sprintf( t('栏目·%s[No.%s]创建申请已提交，请按提示支付写入区块链的手续费') , groupname , groupid ) );
                    // 更新当前用户数据
                    await this.props.store.updateUserInfo();

                    setTimeout( ()=>{
                        this.props.history.push('/group');
                        window.scroll( 0 , 0 );
                    } , 500 );
                    
                }    

            }
        } );
    }

    setEditorRef = (editor) => this.editor = editor;
    
    render()
    {
        const { t } = this.props;
        const main = <div className="blocklist">
            <div className="groupform">
                <form className="px10list">
                    <h1 className="lianmi">{t("创建栏目")}</h1>
                    <FormGroup label={t("栏目名称")} requiredLabel={<span className="require">{t("必填")}</span>}>
                        <input id="name" type="text" placeholder={t("3个以上字符，全站唯一")}  className="pt-input pt-large pt-fill" value={this.state.name} onChange={e=>this.changed(e,"name")} />
                    </FormGroup>
                    <FormGroup label={t("栏目封面")}>
                        <Dropzone className="dropzone" accept="image/png,image/jpg,image/jpeg,image/gif" multiple={false} onDrop={this.handleDrop}>
                            <p className="covertext">{t("点我选择栏目封面图")}</p>
                        </Dropzone>
                        <div className="editbox">
                            <ReactAvatarEditor ref={this.setEditorRef} width={150} height={150}  border={1} image={this.state.image} scale={this.state.scale}  className="theavatar" crossOrigin="anonymous" />
                            <Slider
                                className="slider"
                                min={1}
                                max={5}
                                stepSize={0.1}
                                labelRenderer={false}
                                onChange={(value)=>this.setState({"scale":value})}
                                value={this.state.scale}
                                vertical={true}
                                showTrackFill={true}
                            /> 
                        </div>
                    </FormGroup>
                    <FormGroup label={t("付费会员定价")} requiredLabel={<span className="require">{t("必填")}</span>}>
                        <InputGroup id="price" type="text" placeholder={t("最低0.001ETH/年")}  className="pt-fill" value={this.state.price} onChange={e=>this.changed(e,"price")} large={true}
                        rightElement={<span className="pricepostfix">{t("ETH/年")}</span>}
                        />
                               
                    </FormGroup>
                    <FormGroup label={t("提现地址")} helperText={t("收取的会员费会转到这个地址，请谨慎填写")} requiredLabel={<span className="require">{t("必填")}</span>}>
                        <input id="address" type="text" placeholder={t("ETH(以太坊)钱包地址，形如0x8864xxxxxx")}  className="pt-input pt-large pt-fill" value={this.state.address} onChange={e=>this.changed(e,"address")} autoComplete="eth" />
                    </FormGroup>
                    {/* <Callout intent={Intent.PRIMARY}>
                        {t("由于圈子的分账采用以太坊智能合约，所以创建圈子需要支付少量(0.001ETH)的手续费。")}
                    </Callout> */}

                    <FormGroup>
                        <AnchorButton text={t("创建栏目")} intent={Intent.PRIMARY}  onClick={(e)=>this.create(e)} large={true} />
                    </FormGroup>
                </form>
            </div>
        </div>;
        
        return <Cloumn3Layout left={<UserCard/>} main={main} />;
    }
}