import React, { Component,Fragment } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter, Link  } from 'react-router-dom';
import { translate } from 'react-i18next';
import UserLink from '../component/UserLink'; 
import UserAvatar from '../component/UserAvatar';
import { toast, isApiOk, showApiError, toInt } from '../util/Function';

@withRouter
@translate()
@inject("store")
@observer
export default class MemberItem extends Component
{
    
    state = { "user":null };

    componentDidMount()
    {
        if( this.props.data) 
            this.setState( {"user":this.props.data} );
    }
    
    async blacklist( uid , status )
    {
        const { t } = this.props;
        if( !this.props.group_id || this.props.group_id < 1 ) return toast(t("错误的group_id，请刷新重试"));

        if( toInt(status) === 1 )
        {
            if( !window.confirm(t("加入黑名单的用户将会自动从栏目订户中移除，并不能再次加入，确定要执行？")) ) return false;
        }
        
        const { data } = await this.props.store.setGroupBlacklist( this.props.group_id, uid , status );

        if( isApiOk( data ) )
        {
            if( status === 1 ) toast(t("加入黑名单成功"));
            if( status === 0 ) toast(t("移出黑名单成功"));
            
            let new_user = this.state.user;
            new_user.inblacklist = status;
            this.setState({"user":new_user});
        }
        else
            showApiError( data , t );

    }

    async contribute( uid ,status )
    {
        const { t } = this.props;
        if( !this.props.group_id || this.props.group_id < 1 ) return toast(t("错误的group_id，请刷新重试"));

        const { data } = await this.props.store.setGroupContributeList( this.props.group_id, uid , status );

        if( isApiOk( data ) )
        {
            if( status === 1 ) toast(t("移出投稿黑名单成功"));
            if( status === 0 ) toast(t("加入投稿黑名单成功"));
            
            let new_user = this.state.user;
            new_user.can_contribute = status;
            this.setState({"user":new_user});
        }
        else
            showApiError( data , t );
    }

    async comment( uid ,status )
    {
        const { t } = this.props;
        if( !this.props.group_id || this.props.group_id < 1 ) return toast(t("错误的group_id，请刷新重试"));

        const { data } = await this.props.store.setGroupCommentList( this.props.group_id, uid , status );

        if( isApiOk( data ) )
        {
            if( status === 1 ) toast(t("移出评论黑名单成功"));
            if( status === 0 ) toast(t("加入评论黑名单成功"));
            
            let new_user = this.state.user;
            new_user.can_comment = status;
            this.setState({"user":new_user});
        }
        else
            showApiError( data , t );
    }
    
    render()
    {
        const { t } = this.props;
        const user = this.state.user;
        if( !user ) return null;

        const is_admin = this.props.is_admin ?this.props.is_admin:false;
        const filter = this.props.filter ?this.props.filter:'all';

        const blacklist_menu = toInt(user.inblacklist) === 1 ? <li onClick={()=>this.blacklist( user.id , 0 )}>{t("解除黑名单")}</li>:<li onClick={()=>this.blacklist( user.id , 1 )}>{t("加入黑名单")}</li>;

        const contribute_menu = toInt(user.can_contribute) === 1 ? <li onClick={()=>this.contribute( user.id , 0 )}>{t("加入投稿黑名单")}</li>:<li onClick={()=>this.contribute( user.id , 1 )}>{t("移出投稿黑名单")}</li>;

        const comment_menu = toInt(user.can_comment) === 1 ? <li onClick={()=>this.comment( user.id , 0 )}>{t("加入评论黑名单")}</li>:<li onClick={()=>this.comment( user.id , 1 )}>{t("移出评论黑名单")}</li>;

        let class_name = "title";
        if( toInt( user.inblacklist ) === 1 )  class_name = "title blacklist";
        else
        {
            if( toInt( user.can_comment ) !== 1 || toInt( user.can_contribute ) !== 1 )
            class_name = "title halfblacklist";
        }
        

        const content = <li>
            <div className="image">
                <UserAvatar data={user} className="avatar"/>
            </div>
            <div className="info">
                <div className={class_name}><UserLink data={user} /></div>
                <div className="explain">@{user.username}</div>
            </div>
            { is_admin && <div className="actionbar">
                { filter === 'all' && toInt(user.inblacklist) === 1  && <ul className="actions">
                    {blacklist_menu} 
                </ul> }

                { filter === 'all' && toInt(user.inblacklist) !== 1  && <ul className="actions">
                    {blacklist_menu} {contribute_menu}{comment_menu}
                </ul> }
                

                { filter === 'blacklist' && <ul className="actions">
                    {blacklist_menu}
                </ul> }
                { filter === 'contribute' && <ul className="actions">
                    {contribute_menu}
                </ul> }
                { filter === 'comment' && <ul className="actions">
                    {comment_menu}
                </ul> }
            </div>}
            </li>;

        return content;
    }
}