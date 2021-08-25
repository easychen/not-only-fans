import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { translate , Trans } from 'react-i18next';
import { withRouter } from 'react-router-dom';
import { Button, FormGroup, Intent,  Tab, Tabs} from "@blueprintjs/core";
import Icon from '../Icon';
import { toast , showApiError, is_fo_address } from '../util/Function';
import LangIcon from '../component/LangIcon';
import { sprintf } from 'sprintf-js';
import ScrollTopView from '../component/ScrollTopView';
// import Web3 from 'web3';
import DocumentTitle from 'react-document-title';
import CookieConsent from "react-cookie-consent";

@translate()
@inject("store")
@withRouter
@observer
export default class Front extends Component
{
    state={
        "tabid":"register"
        ,"email":""
        ,"nickname":""
        ,"username":""
        ,"password":""
        ,"password2":""
        ,"address":""
    }

    componentDidMount()
    {
        //console.log( this.props );
        if( this.props.location.pathname === '/login' )
        {
            this.setState( {"tabid":"login"} );
        }
        else
        {
            this.setState( {"tabid":"register"} )
        }
    }
    
    handleTabChange( value )
    {
        //console.log( e );
        this.setState( {"tabid":value} );
    }

    changed( e , name )
    {
        let o = {};
        o[name] =  e.target.value ;
        this.setState( o ) ;
    }

    async register( e )
    {
        const { t } = this.props;

        if( this.state.email.length < 1 ) return toast(t("Email地址不能为空"));
        if( this.state.nickname.length < 1 ) return toast(t("用户昵称地址不能为空"));
        if( this.state.username.length < 3 ) return toast(t("UserName不能短于3位"));
        if( this.state.password.length < 6 ) return toast(t("密码不能少于6个字符"));
        if( this.state.password !== this.state.password2) return toast(t("两次输入的密码不一致"));

        if( this.state.address.length > 0 && !is_fo_address( this.state.address  ) )
        {
            return toast(t("请输入正确的账户，没有可以留空"));
        }

        const { data } = await this.props.store.register( this.state.email , this.state.nickname , this.state.username , this.state.password, this.state.address );

        if( !showApiError( data , t ) )
        {
            // console.log(  data );
            // 注册成功
            toast( data.data.nickname + t('注册成功，请登入'));
            this.setState( {"tabid":"login"} );
        }        
    }

    async login( e )
    {
        const { t } = this.props;

        if( this.state.email.length < 1 ) return toast(t("Email地址不能为空"));
        if( this.state.password.length < 6 ) return toast(t("密码不能少于6个字符"));
        
        const { data } = await this.props.store.login( this.state.email , this.state.password );

        if( !showApiError( data , t ) )
        {
            const username = data.data && data.data.nickname ? data.data.nickname : '';
            
            const red_path = data.data.group_count == 0 ? '/group' : '/';
            const welcome = data.data.group_count == 0 ? '欢迎%s，订阅了栏目才有东西可以看哦' : '欢迎回来%s' ;

            toast( sprintf( t(welcome) , username ) );
            setTimeout( ()=>this.props.history.push(red_path) , 500 );
            
        }        
    }

    render()
    {
        const { t } = this.props;

        const appname = this.props.store.appname;
        
        return <DocumentTitle title={t(appname)}><div className="loginhero">
        
        <div className="logobox">
            <div className="logo-div"><Icon name="logo" size={120} color="white" /></div>

            <div className="title">{t('欢迎来到NotOnlyFans，基于加密货币的数字内容订阅服务')}<LangIcon className="front"/></div>
        </div>
        
        
    

        <Tabs className="fronttab" onChange={e => this.handleTabChange(e)} selectedTabId={this.state.tabid}>
            <Tab id="register" title={t("注册")} panel={<form className="loginform">
                <FormGroup
                    label="Email"
                    labelFor="email"
                    requiredLabel={<span className="require">{t("必填")}</span>}
                >
                    <input id="email" type="email" placeholder={t("邮件地址")}  className="pt-input pt-large pt-fill" value={this.state.email} onChange={e=>this.changed(e,"email")} autoComplete='email' />
                </FormGroup>
                <FormGroup
                    label={t("用户昵称")}
                    labelFor="nickname"
                    requiredLabel={<span className="require">{t("必填")}</span>}
                >
                    <input id="nickname" placeholder={t("昵称")}  className="pt-input pt-large pt-fill" value={this.state.nickname} onChange={e=>this.changed(e,"nickname")} autoComplete='username'  />
                </FormGroup>

                <FormGroup
                    label={t("UserName")}
                    labelFor="username"
                    requiredLabel={<span className="require">{t("必填")}</span>}
                >
                    <input id="username" placeholder={t("用户唯一标识，只能由英文、数字构成，全站唯一，最短3位，不可修改")} className="pt-input pt-large pt-fill" value={this.state.username} onChange={e=>this.changed(e,"username")}/>
                </FormGroup>

                <FormGroup
                    label={t("密码")}
                    labelFor="password"
                    requiredLabel={<span className="require">{t("必填")}</span>}
                >
                    <input id="password" placeholder={t("登入密码，最短6位")}  type="password" className="pt-input pt-large pt-fill" value={this.state.password} onChange={e=>this.changed(e,"password")} autoComplete="new-password" />
                </FormGroup>

                <FormGroup
                    label={t("重复输入密码")}
                    labelFor="password2"
                    requiredLabel={<span className="require">{t("必填")}</span>}
                >
                    <input id="password2" placeholder={t("再次输入密码确认")}  type="password" className="pt-input pt-large pt-fill" value={this.state.password2} onChange={e=>this.changed(e,"password2")} autoComplete="new-password"/>
                </FormGroup>

                {/* <FormGroup
                    label={t("FO账户")}
                    labelFor="address"
                >
                    <input id="address" placeholder={t("FO账户，用于接收赞赏、收取小组会费等，没有可不填")} className="pt-input pt-large pt-fill" value={this.state.address} onChange={e=>this.changed(e,"address")}/>
                </FormGroup> */}
                <div className="actionbar">
                    <div className="left">
                        <Button id="lm-register-btn" large={true} intent={Intent.PRIMARY} text={t("注册")} onClick={e=>this.register(e)} />
                    </div>
                    
                </div>

                </form>} />
            <Tab id="login" title={t("登入")} panel={<form className="loginform">
                <FormGroup
                    label="Email"
                    labelFor="email2"
                    requiredLabel={<span className="require">{t("必填")}</span>}
                >
                    <input id="email2" placeholder={t("邮件地址")}  className="pt-input pt-large pt-fill" value={this.state.email} onChange={e=>this.changed(e,"email")} autoComplete="email"/>
                </FormGroup>

                <FormGroup
                    label={t("密码")}
                    labelFor="password1"
                    requiredLabel={<span className="require">{t("必填")}</span>}
                >
                    <input id="password1" placeholder={t("登入密码，最短6位")}  type="password" className="pt-input pt-large pt-fill" value={this.state.password} onChange={e=>this.changed(e,"password")} autoComplete="password"/>
                </FormGroup>

                <div className="actionbar">
                    <div className="left">
                        <Button id="lm-login-btn" large={true} intent={Intent.PRIMARY} text={t("登入")} onClick={e=>this.login(e)}/>
                    </div>

                </div>

            </form>} />    
        </Tabs>
        <ScrollTopView/>
        <CookieConsent>
            This website uses cookies to enhance the user experience.
        </CookieConsent>
        </div></DocumentTitle>
    }
} 