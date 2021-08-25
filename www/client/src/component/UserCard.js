import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import UserLink from './UserLink';
import UserAvatar from './UserAvatar';
import { toInt } from '../util/Function';

@translate()
@inject("store")
@withRouter
@observer
export default class UserCard extends Component
{
    render()
    {
        const { t } = this.props;
        // 如果不传参数，使用当前用户
        const user = this.props.user ? this.props.user : this.props.store.user;
        const usercard_cover = user.cover ? user.cover : '/usercard_cover.png';

        if( toInt( user.id ) === 0 ) return  null;
        
        return <div className="usercard">
        <div className="cover" style={{'backgroundImage':"url('"+ usercard_cover +"')"}}></div>
        <div className="info">
            <div className="avatar"><UserAvatar data={user} className="cardavatar" />
            <div className="username">
                <div className="title"><UserLink data={user} /></div>
                <div className="desp">@{user.username}</div>
            </div>    
            </div>

            <div className="count">
                <div className="groupcount">
                    <span>{t("栏目")}</span>
                    <h1>{parseInt( user.group_count , 10 )}</h1>
                    
                </div>
                <div className="feedcount">
                    <span>{t("内容")}</span>
                    <h1>{parseInt( user.feed_count , 10 )}</h1>
                </div> 

                <div className="upcount">
                    <span>{t("被赞")}</span>
                    <h1>{parseInt( user.up_count , 10 )}</h1>
                </div> 

            </div> 
        </div>    
        </div>;   
    }
}