import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';

import { Menu , MenuItem, Popover, PopoverInteractionKind, Position, Icon } from "@blueprintjs/core";
import { isApiOk, toInt, toast, showApiError } from '../util/Function';

@translate()
@inject("store")
@withRouter
@observer
export default class UserMenu extends Component
{
    async logout()
    {
        const { t } = this.props;
        const { data } = await this.props.store.logout();
        if( isApiOk( data ) )
        {
            if( toInt( data.data ) == 1  )
            {
                toast(t("已成功退出"));
                this.props.history.replace('/login');
            }
        }else
            showApiError( data , t ); 
    }
    
    render()
    {
        const { t } = this.props;
        const { user } = this.props.store;
    
    
        return <Popover content={<Menu>
            <MenuItem icon="document"  text={t("修改资料")} onClick={()=>this.props.history.push('/settings/profile')} />
            <MenuItem icon="user"  text={t("修改头像")} onClick={()=>this.props.history.push('/settings/avatar')}/> 
            <MenuItem icon="lock"  text={t("修改密码")} onClick={()=>this.props.history.push('/settings/password')}/>
            <MenuItem icon="settings"  text={t("偏好设置")} onClick={()=>this.props.history.push('/settings/preference')}/>
            
            <MenuItem icon="log-out"  text={t("退出")} onClick={()=>this.logout()}/>   
        </Menu>} position={Position.BOTTOM} interactionKind={PopoverInteractionKind.CLICK}>
        <span className="text usermenuname">{user.nickname.substring(0,12)}<Icon icon="caret-down"/></span>
        </Popover>;
    }
}