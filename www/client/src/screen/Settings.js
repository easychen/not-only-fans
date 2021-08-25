import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';

import Cloumn3Layout from '../component/Cloumn3Layout';
import UserCard from '../component/UserCard';
import DocumentTitle from 'react-document-title';
import ActivityLink from '../util/ActivityLink';
import Dropzone from 'react-dropzone';
import ReactAvatarEditor from 'react-avatar-editor';
import Web3 from 'web3';

import { Button, FormGroup, Intent, Slider, Spinner, NonIdealState } from "@blueprintjs/core";
import { isApiOk, showApiError, toast, is_fo_address } from '../util/Function';
import VisibilitySensor from 'react-visibility-sensor';

import BlacklistItem from '../component/BlacklistItem'; 


@withRouter
@translate()
@inject("store")
@observer
export default class Settings extends Component
{
    
    constructor( props )
    {
        super( props );

        const avatar = this.props.store.user.avatar ? this.props.store.user.avatar: '/cover.png';
        const cover = this.props.store.user.cover ? this.props.store.user.cover : '/cover.png';

        this.state={ "user":null,"avatar":avatar,"cover":cover,"scale":1,"blacklist":[],"since_id":0 };
    }
    
    

    componentDidMount()
    {
        if( !Web3.givenProvider )
       {
           toast(this.props.t("请先安装MetaMask等插件"));
       }else
       {
        this.web3 = new Web3(Web3.givenProvider);
       }   
        
        const filter = this.props.match.params.filter ? this.props.match.params.filter : 'profile';

        //if( filter === 'blacklist' )
            this.loadBlacklist();
        //else
            this.loadUserData();
    }

    // async componentDidUpdate(prevProps) 
    // {
    //     if (this.props.location !== prevProps.location) 
    //     {
    //         const filter = this.props.match.params.filter ? this.props.match.params.filter : 'profile';

    //         if( filter === 'blacklist' )
    //             this.loadBlacklist();
    //         else
    //             this.loadUserData();
    //     }
    // }

    async loadBlacklist( clean = false , sid = null )
    {
        console.log("IN");
        const { t } = this.props;
        const since_id = sid === null ? this.state.since_id : sid;

        const { data } = await this.props.store.getUserBlacklist( since_id );
        this.setState({"loading":false});

        if( isApiOk( data ) )
        {
            //this.setState( {"blacklist":data.data.blacklist} );
            if( data.data !=  undefined  )
            {
                console.log( data.data );
                if( !Array.isArray(data.data.blacklist) ) data.data.blacklist =[];
                
                let since_id_new = null;
                if( data.data.minid !=  null )
                {
                    const minid = parseInt( data.data.minid , 10 );
                    since_id_new = minid;
                }
                
                const newdata = clean ? data.data.blacklist :this.state.blacklist.concat(data.data.blacklist);

                if( since_id_new === null )
                    this.setState({"blacklist":newdata});
                else  
                    this.setState({"blacklist":newdata,"since_id":since_id_new});  
            }
        }
        else
            showApiError( data , t );
    }

    blacklistloading( visible )
    {
        if( visible )
        {
            // 发生变动且能看到底部，进行数据加载
            if( this.state.since_id != 0 )
            {
                this.setState({"loading":true});
                setTimeout(() => {
                    this.loadBlacklist();
                }, 0);
            }
                
        }
        //console.log(e);
    }

    async loadUserData()
    {
        const { t } = this.props;
        // 读取服务器端的信息
        const { data } = await this.props.store.getUserDetail( this.props.store.user.uid );

        if( isApiOk( data ) )
        {
            data.data.old_password = '';
            data.data.password = '';
            data.data.password2 = '';
            this.setState( {"user":data.data} );
        }
        else
            showApiError( data , t );
    }
    
    changed( e , name )
    {
        let o = this.state.user;
        o[name] =  e.target.value ;
        this.setState( {"user":o} ) ;
    }

    handleDrop( dropped , type ) 
    {
        if( type === 'avatar' )
            this.setState({ 'avatar':dropped[0] });
        else
            this.setState({ 'cover':dropped[0] });    
    }

    async profile()
    {
        const { t } = this.props;
        // 更新资料
        // 检查参数
        if( this.state.user.nickname.length < 1 )
            return toast(t("用户昵称不能为空"));

        if( this.state.user.address.length > 0 && !this.web3.utils.isAddress( this.state.user.address  ) )
            return toast(t("请输入正确的钱包地址，没有可以留空"));
        
        const { data } = await this.props.store.updateUserProfile( this.state.user.nickname , this.state.user.address );

        if( isApiOk( data ) )
        {
            // 更新成功
            toast(t("资料已成功保存"));
            this.props.store.updateUserInfo();
        }
        else
            showApiError( data , t );

    }

    async password()
    {
        const { t } = this.props;
        if( this.state.user.password != this.state.user.password2 )
            return toast(t("两次输入的密码不一致"));
        
        if( this.state.user.old_password.length < 6 )
            return toast(t("原密码不能短于6位"));

        if( this.state.user.password.length < 6 )
            return toast(t("新密码不能短于6位")); 
            
        const { data } = await this.props.store.updateUserPassword( this.state.user.old_password , this.state.user.password );

        if( isApiOk( data ) )
        {
            // 更新成功
            toast(t("密码已经修改，请重新登入"));
            const result = await this.props.store.logout();
            if( isApiOk( result.data ) )
            {
                this.props.history.push('/login');
            }
            else
            {
                showApiError( result.data , t );
            }
            //this.props.history.push('/logout');
            // this.props.store.updateUserInfo();
        }
        else
            showApiError( data , t );    
    }

    async avatar()
    {
        const { t } = this.props;
        this.avatarEditor.getImageScaledToCanvas().toBlob( async (blob) => 
        {
            const { data } = await this.props.store.uploadCover( blob );
            if( isApiOk( data ) )
            {
                // 封面图片上传完成，地址为 data.data.url
                
                if( !data.data.url || data.data.url.length < 1 )
                {
                    toast(t("图片上传失败，请重试"));
                    return false;
                }

                const image_url = data.data.url;

                const result = await this.props.store.updateUserAvatar( image_url );

                if( isApiOk( result.data ) )
                {
                    toast(t("头像已成功保存"));
                    this.props.store.updateUserInfo();
                }
                else
                    showApiError( result.data , t );

            }else
                showApiError( data , t );
        } );
    }

    async cover()
    {
        const { t } = this.props;
        this.coverEditor.getImageScaledToCanvas().toBlob( async (blob) => 
        {
            const { data } = await this.props.store.uploadCover( blob );
            if( isApiOk( data ) )
            {
                // 封面图片上传完成，地址为 data.data.url
                
                if( !data.data.url || data.data.url.length < 1 )
                {
                    toast(t("图片上传失败，请重试"));
                    return false;
                }

                const image_url = data.data.url;

                const result = await this.props.store.updateUserCover( image_url );

                if( isApiOk( result.data ) )
                {
                    toast(t("背景封面已成功保存"));
                    this.props.store.updateUserInfo();
                }
                else
                    showApiError( result.data , t );

            }else
                showApiError( data , t );
        } );
    }

    render()
    {
        const { t } = this.props;

        const profile_box = this.state.user && <div className="formbox">
        <form className="px10list">
            <FormGroup label={t("电子邮件")} requiredLabel={<span className="require">{t("必填")}</span>} >
                <input id="email" type="text" placeholder={t("电子邮件")}  className="pt-input pt-fill pt-large" value={this.state.user.email} disabled={true} title={t("不可修改")}/>
            </FormGroup>
            <FormGroup label={t("UserName")} requiredLabel={<span className="require">{t("必填")}</span>} >
                <input id="username" type="text" placeholder={t("UserName")}  className="pt-input pt-fill pt-large" value={this.state.user.username} disabled={true} title={t("不可修改")}/>
            </FormGroup>
            <FormGroup label={t("昵称")} requiredLabel={<span className="require">{t("必填")}</span>} >
                <input id="nickname" type="text" placeholder={t("昵称")}  className="pt-input pt-fill pt-large" value={this.state.user.nickname} onChange={e=>this.changed(e,"nickname")} />
            </FormGroup>
            <FormGroup label={t("钱包地址")} >
                <input id="address" type="text" placeholder={t("以太坊钱包地址，用于接收赞赏之用，没有可不填")}  className="pt-input pt-fill  pt-large" value={this.state.user.address} onChange={e=>this.changed(e,"address")} />
            </FormGroup>

            <div className="actionbar">
                <div className="left">
                    <Button large={true} id="lm-profile-update-btn" intent={Intent.PRIMARY} text={t("更新")} onClick={e=>this.profile(e)} />
                </div>
                
            </div>
        </form>
        </div>;

        const avatar_box = this.state.user && <div className="formbox">
        <form className="px10list">
        <FormGroup label={t("头像照片")}>
            <div className="editbox">
                <ReactAvatarEditor ref={(editor)=>this.avatarEditor=editor} width={150} height={150}  border={1} image={this.state.avatar} scale={this.state.scale} crossOrigin="anonymous" className="theavatar"/>
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
        <div className="buttonbar_inline">
            <Dropzone className="dropzone" accept="image/png,image/jpg,image/jpeg,image/gif" multiple={false} onDrop={(e)=>this.handleDrop(e,'avatar')}>
                <Button large={true} intent={Intent.NONE} text={t("选择头像照片")} />
            </Dropzone>

            <Button large={true} intent={Intent.PRIMARY} text={t("更新")} onClick={e=>this.avatar(e)} />
            
        </div>    
        </form></div>;
        
        const password_box = this.state.user && <div className="formbox">
        <form className="px10list">
            
            <FormGroup
                label={t("原密码")}
                labelFor="old_password"
                requiredLabel={<span className="require">{t("必填")}</span>}
            >
                <input id="old_password" placeholder={t("登入密码，最短6位")}  type="password" className="pt-input pt-large pt-fill" value={this.state.user.old_password} onChange={e=>this.changed(e,"old_password")} autoComplete="new-password" />
            </FormGroup>

            <FormGroup
                    label={t("新密码")}
                    labelFor="password"
                    requiredLabel={<span className="require">{t("必填")}</span>}
                >
                    <input id="password" placeholder={t("登入密码，最短6位")}  type="password" className="pt-input pt-large pt-fill" value={this.state.user.password} onChange={e=>this.changed(e,"password")} autoComplete="new-password" />
                </FormGroup>

                <FormGroup
                    label={t("重复输入新密码")}
                    labelFor="password2"
                    requiredLabel={<span className="require">{t("必填")}</span>}
                >
                    <input id="password2" placeholder={t("再次输入密码确认")}  type="password" className="pt-input pt-large pt-fill" value={this.state.user.password2} onChange={e=>this.changed(e,"password2")} autoComplete="new-password"/>
                </FormGroup>

            <div className="actionbar">
                <div className="left">
                    <Button large={true} intent={Intent.PRIMARY} text={t("更新")} onClick={e=>this.password(e)} />
                </div>
                
            </div>
        </form>
        </div>;

        const preference_box = this.state.user && <div className="formbox">
        <form className="px10list">
        <FormGroup label={t("个人卡片封底背景")}>
            <div className="editbox">
                <ReactAvatarEditor ref={(editor)=>this.coverEditor=editor} width={400} height={168}  border={1} image={this.state.cover} scale={this.state.scale} className="theavatar" crossOrigin="anonymous"/>
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
        <div className="buttonbar_inline">
            <Dropzone className="dropzone" accept="image/png,image/jpg,image/jpeg,image/gif" multiple={false} onDrop={(e)=>this.handleDrop(e,'cover')}>
                <Button large={true} intent={Intent.NONE} text={t("选择背景照片")} />
            </Dropzone>

            <Button large={true} intent={Intent.PRIMARY} text={t("更新")} onClick={e=>this.cover(e)} />
            
        </div>    
        </form></div>;

        const blacklist_box = <div>
        { this.state.blacklist && this.state.blacklist.length > 0 && <div><ul className="memberlist">
            {this.state.blacklist.map( (item) => <BlacklistItem data={item.user} key={item.id}  /> ) } 
        </ul> 
        { this.state.loading && <div className="hcenter"><Spinner intent={Intent.PRIMARY} small={true} /></div> }
        <VisibilitySensor onChange={(e)=>this.blacklistloading(e)}/>
        </div>
            
        }
        
        
        
        { (!this.state.blacklist || this.state.blacklist.length < 1) && <NonIdealState className="padding40"
                visual="search"
                title={t("黑名单为空")}
                description={t("没有遇到傻x真是太好了")}
                
        /> }
        </div>;

        const filter = this.props.match.params.filter ? this.props.match.params.filter : 'profile';

        let main_box = '';
        eval( "main_box = "+filter+"_box ? "+filter+"_box : ''" );
        
        const main = <div className="blocklist">
            <div className="feedfilter sticky">
                <div >
                    <ActivityLink label={t("资料")} to={"/settings/profile"} />
                </div>
                <div >
                <ActivityLink label={t("头像")} to={"/settings/avatar"} />
                </div>
                <div>
                <ActivityLink label={t("密码")} to={"/settings/password"} />
                </div>
                <div>
                <ActivityLink label={t("偏好")} to={"/settings/preference"} />
                </div>
                <div>
                <ActivityLink label={t("黑名单")} to={"/settings/blacklist"} />
                </div>

            </div>   
            <div className="settingbox">{main_box}</div>     
        </div>;

        

        return <DocumentTitle title={t('设置')+'@'+this.props.store.appname}><Cloumn3Layout left={<UserCard/>} main={main} /></DocumentTitle>;
    }
}