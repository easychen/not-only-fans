import React, { Component , Fragment } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import Linkify from 'react-linkify';

import { Icon } from "@blueprintjs/core";


import MyTime from '../component/MyTime'; 
import UserLink from '../component/UserLink'; 
import UserAvatar from '../component/UserAvatar'; 
import { isApiOk, showApiError, toast } from '../util/Function';


@withRouter
@translate()
@inject("store")
@observer
export default class CommentItem extends Component
{
    state = {"show":true};

    async removeComment( id )
    {
        const { t } = this.props;
        
        if( !window.confirm(t('确定要删除这条评论吗？')) ) return false;

        const { data } = await this.props.store.removeFeedComment( id );
        if( isApiOk( data ) )
        {
            toast(t("评论已成功删除"));
            this.setState({"show":false});
            if( this.props.onRemove ) this.props.onRemove();
        }
        else
            showApiError( data , t );
    }
    
    render()
    {
        const item = this.props.data ? this.props.data : null; 
        const can_delete = item ? ( item.user.id == this.props.store.user.id || ( this.props.admin && this.props.admin == this.props.store.user.id ) ) : false;
        const { t } = this.props;

        return <Fragment>{ (this.state.show && item) && <li className="commentitem">
        <div className="content">
            <UserAvatar data={item.user} className="avatar"/>
            { can_delete && <div className="delete" ><Icon icon="small-cross" title={t("删除评论")} onClick={()=>this.removeComment(item.id)}/></div> }
            <UserLink data={item.user} /><span className="explain left5">@{item.user.username}</span>
            <Linkify properties={{target: '_blank'}} className="left5">{item.text}</Linkify>

            

        </div>
        <div><span className="timeline"><MyTime date={item.timeline} /></span></div>
        
        </li> }</Fragment>;
    }
}