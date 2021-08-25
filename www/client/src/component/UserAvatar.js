import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter, Link } from 'react-router-dom';
import { translate } from 'react-i18next';

@withRouter
@translate()
@inject("store")
@observer
export default class UserAvatar extends Component
{
    render()
    {
        const user = this.props.data ? this.props.data : null;
        if( !user.avatar ) user.avatar = '/image/avatar.jpg';
        
        const content = user ? <Link to={'/user/'+user.id} target="_blank" title={'@'+user.username} className={this.props.className}><img src={user.avatar} /></Link> : null;
        return content;
    }
}