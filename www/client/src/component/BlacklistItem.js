import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import UserLink from '../component/UserLink'; 
import UserAvatar from '../component/UserAvatar';
import { toInt, toast, isApiOk, showApiError } from '../util/Function';
@withRouter
@translate()
@inject("store")
@observer
export default class BlacklistItem extends Component
{
    state = { "user":null,"in":true };

    componentDidMount()
    {
        if( this.props.data) 
            this.setState( {"user":this.props.data} );
    }

    async setBlacklist( uid , status )
    {
        const { t } = this.props;
        const { data } = await this.props.store.setUserInBlacklist( uid , status );
        if( isApiOk( data ) )
        {
            if( toInt( data.data )  === 1 )
                toast(t("已将此用户加入黑名单，你可以在设置中移出"));
            else
                toast(t("已将此用户移出黑名单"));

            this.setState({"in":toInt(status)===1});    
        }
        else
            showApiError( data , t  );
    }

    render()
    {
        const { t } = this.props;
        const user = this.state.user;
        if( !user ) return null;

        const menu = this.state.in ? <li onClick={()=>this.setBlacklist( user.id , 0 )}>{t("移出黑名单")}</li>:<li onClick={()=>this.setBlacklist( user.id , 1 )}>{t("加入黑名单")}</li>;

        const class_name = this.state.in ? "title blacklist" : "title";

        return <li>
        <div className="image">
            <UserAvatar data={user} className="avatar"/>
        </div>
        <div className="info">
            <div className={class_name}><UserLink data={user} /></div>
            <div className="explain">@{user.username}</div>
        </div>
        <div className="actionbar">
        <ul className="actions">{menu}</ul>
        </div>
        </li>;
    }
}