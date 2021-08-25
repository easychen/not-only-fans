import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter, Link } from 'react-router-dom';
import { translate } from 'react-i18next';
import UserAvatar from './UserAvatar';
import UserLink from './UserLink';

@withRouter
@translate()
@inject("store")
@observer
export default class SystemNoticeAction extends Component
{
    render()
    {
        const { t } = this.props;
        const action = this.props.data ? this.props.data : null;
        
        return action && <div>
        <UserLink data={action} /><span className="left5 explain">{action.username}</span> <Link to={action.link} target="_blank">{t(action.action)}</Link>
        </div>;
    }
}