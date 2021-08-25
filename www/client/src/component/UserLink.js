import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter, Link } from 'react-router-dom';
import { translate } from 'react-i18next';
import { toInt } from '../util/Function';

@withRouter
@translate()
@inject("store")
@observer
export default class UserLink extends Component
{
    render()
    {
        const { t } = this.props;
        const user = this.props.data ? this.props.data : null;
        let user2 = {...user};
        if( user2.nickname ) user2.nickname = user2.nickname.substring(0,12);

        const content = user2 ? ( user2.id ?  <Link to={'/user/'+user2.id} title={'@'+user2.username} className={this.props.className + ' userlink'}>{user2.nickname}</Link> : user2.nickname ): null;

        const admin = <span className={this.props.className}>{t("SYSTEM")}</span>

        // console.log( "user2" , user2 );

        if( !user2 ) 
            return null;
        else
            return toInt( user2.uid ) === 0 ? admin : content;
    }
}