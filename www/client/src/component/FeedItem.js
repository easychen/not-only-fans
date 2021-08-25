import React, { Component, Fragment } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter, Link } from 'react-router-dom';
import { translate } from 'react-i18next';

import MyTime from '../component/MyTime'; 

import { Colors, Icon, Menu , MenuItem, MenuDivider, Popover, PopoverInteractionKind, Position, InputGroup, Button, TextArea, AnchorButton } from "@blueprintjs/core";
import LMIcon from '../Icon';


import FeedText from '../component/FeedText';
import { toast, isApiOk, showApiError, toInt } from '../util/Function';
import CommentItem from '../component/CommentItem'; 
import UserLink from '../component/UserLink'; 
import UserAvatar from '../component/UserAvatar';
import ModalImage from "react-modal-image";



@withRouter
@translate()
@inject("store")
@observer
export default class FeedItem extends Component
{
    
    constructor( props )
    {
        super( props );

        let item = this.props.data;
        const show_comment = this.props.show_comment ? true:false;

        if(item.images && !Array.isArray(item.images) && item.images.length > 0)
            item.images = JSON.parse( item.images );

        // if(item.files && !Array.isArray(item.files) && item.files.length > 0)
        //     item.files = JSON.parse( item.files ); 
        if( item.files )
        {
            const  fdata = JSON.parse( item.files );
            console.log( "fdata" , fdata );
            if( fdata.url && fdata.name  )
                item.files =  [fdata];
            else
                item.files = false;    
        }
        

        this.state = {"feed":item,"show_comment":show_comment,"commment_text":'',"comments":[],"since_id":0,"total":0,"comments_per_feed":0};   
        
    }

    componentDidMount()
    {
        if( this.props.show_comment ) this.loadComments();
    }
    
    
    
    edit( item )
    {
        
        const { store } = this.props;
        store.draft_feed_id = item.id;
        store.draft_text = item.text;
        store.draft_images = Array.isArray(item.images) ? item.images : []  ;

        if( item.files && item.files[0] )
        {
            store.draft_attachment_name = item.files[0]['name'];
            store.draft_attachment_url = item.files[0]['url'];
        }
        store.draft_is_paid = item.is_paid;
        
        store.draft_update_callback = ()=>{this.update( store.draft_text , store.draft_images, [{"url":store.draft_attachment_url,"name":store.draft_attachment_name}]  , store.draft_is_paid )};
        
        store.float_editor_open = true;

        console.log( store.draft_images );
    }

    update( text, images, files , is_paid )
    {
        let feed = this.props.data;
        feed.text = text;
        feed.images = images;
        feed.files = files;
        // feed.images = JSON.stringify(images);
        feed.is_paid = is_paid;
        // console.log( "new " , feed );
        this.setState({"feed":feed});
    }

    open( id )
    {
        window.open('/feed/'+id);
    }

    toggle_comment()
    {
        if( this.state.show_comment === false )
        {
            if( this.state.comments.length < 1 )
                this.loadComments();
        }
        this.setState({"show_comment":!this.state.show_comment});
        
    }

    async remove( id )
    {
        const { t } = this.props;
        if(window.confirm(t("确认要删除这条内容么？")))
        {
            const { data } = await this.props.store.removeFeed( id );
            if( isApiOk ( data ) )
            {
                toast(t("内容已删除"));
                let feed = this.state.feed;
                feed.is_delete = 1;
                this.setState({"feed":feed});
            }
            else
            {
                showApiError( data , t );         
            }
        }
    }

    handleComment(e)
    {
        this.setState({"commment_text":e.target.value})
    }

    async loadComments( clean = false )
    {
        const { t } = this.props;
        const since_id = clean ? 0 : this.state.since_id ;
        const { data } = await this.props.store.getFeedComments( this.props.data.id , since_id );

        if( isApiOk( data ) )
        {
            if( data.data !==  undefined  )
            {
                if( !Array.isArray(data.data.comments) ) data.data.comments =[];
                
                let since_id_new = null;
                if( data.data.minid !=  null )
                {
                    const minid = parseInt( data.data.minid , 10 );
                    since_id_new = minid;
                }
                
                const newdata = clean ? data.data.comments : this.state.comments.concat(data.data.comments);

                if( since_id_new === null )
                    this.setState({"comments":newdata,"total":parseInt(data.data.total,10),"comments_per_feed":parseInt(data.data.comments_per_feed,10)});
                else  
                    this.setState({"comments":newdata,"since_id":since_id_new,"total":parseInt(data.data.total,10),"comments_per_feed":parseInt(data.data.comments_per_feed,10)});  
            }
        }else
        showApiError( data , t );
    }

    async sendComment()
    {
        const { t } = this.props;
        if( this.state.commment_text.length < 1 )
        {
            toast(t("评论不能为空"));
            return false;
        }

        // 开始发送评论
        const { data } = await this.props.store.saveFeedComment( this.props.data.id , this.state.commment_text );

        if( isApiOk( data ) )
        {
            // 更新feed的评论计数
            let feed = this.state.feed;
            feed.comment_count++;
            this.setState({"commment_text":'',"feed":feed});
            toast(t("评论发布成功"));
            
            // 当浏览的评论数量超过了单页数量，不再刷新内容，以免造成正在阅读的数据丢失
            if( this.state.comments.length <= this.state.comments_per_feed )
                this.loadComments( true );
            
        }else
        showApiError( data , t );
    }

    commentRemoved()
    {
        // 更新feed的评论计数
        let feed = this.state.feed;
        if( feed.comment_count > 0 )
            feed.comment_count--;
        
            this.setState({"commment_text":'',"feed":feed});
    }

    async topit( item , status = 1 )
    {
        const { t } = this.props;

        const { data } = await this.props.store.groupSetTop( item.group.id , item.id , status );
        if( isApiOk( data ) )
        {
            if( data.data.top_feed_id == 0 )
            {
                toast(t("已取消置顶，刷新后可见"));
            }
            else
            {
                toast(t("已设为栏目置顶，刷新后可见")); 
            }
            // console.log( data );
        }else
        showApiError( data , this.props.t );

        // // 开始发送评论
        // const { data } = await this.props.store.feedSetTop( this.props.data.id , status );
        // if( isApiOk( data ) )
        // {
        //     console.log( data );
        // }else
        // showApiError( data , this.props.t );
    }
    
    render()
    {
        const { t } = this.props;
        const item = this.state.feed;
        const admin_uid = toInt(item.is_forward) === 1 ? item.forward_uid : item.uid;
        
        // console.log( "init", item );

        const hiddenclass = item.is_delete && parseInt( item.is_delete , 10 ) === 1 ? 'hiddenitem' : '';
        
        let i = 0;

        let from = toInt(item.forward_group_id) !== 0 ? <span>&nbsp;·&nbsp;
        来自&nbsp;<Link to={'/group/'+item.group.id} >{item.group.name} </Link></span>: '';

        if( this.props.in_group ) from = '';

        let actionMenu = '';
        const menu_delete = <MenuItem icon="cross"  text={t("删除")} onClick={()=>this.remove(item.id)}/>;
        const menu_edit = <MenuItem icon="edit"  text={t("编辑")} onClick={()=>this.edit(item)}/>;
        const menu_open = <MenuItem icon="document-open"  text={t("查看")} onClick={()=>this.open(item.id)}/>;

        const menu_top = item.id == item.group.top_feed_id ? <MenuItem icon="arrow-down"  text={t("取消置顶")} onClick={()=>this.topit(item,0)}/>: <MenuItem icon="arrow-up"  text={t("置顶")} onClick={()=>this.topit(item,1)}/>;

        // const menu_top = <MenuItem icon="arrow-up"  text={t("置顶")} onClick={()=>this.topit(item,1)}/>;
        

        // 转发的情况
        if( toInt(item.is_forward) === 1 )
        {
            // 当前用户是栏主（即转发人）
            if( toInt(item.forward_uid) === toInt(this.props.store.user.id) )
            {
                // 栏主转发自己的内容(直发)
                if( toInt(item.uid) ===  toInt(this.props.store.user.id) )
                {
                    // 所有权限
                    actionMenu = <Menu>{menu_delete}{menu_edit}{menu_top}<MenuDivider />{menu_open}</Menu>;
                }
                else 
                {
                    // 栏主转发别人的内容(投稿)
                    // 只支持删除（而且删除的是转发），不支持编辑
                    actionMenu = <Menu>{menu_delete}{menu_top}<MenuDivider />{menu_open}</Menu>;
                }
            }
            else
            {
                // 路人，支持查看
                actionMenu = <Menu>{menu_open}</Menu>;
            } 
        }
        else
        {
            // 原发的情况，这时候应该是在用户个人页面上
            if( toInt(item.uid) ===  toInt(this.props.store.user.id) )
            {
                // 所有权限
                actionMenu = <Menu>{menu_delete}{menu_edit}<MenuDivider />{menu_open}</Menu>;
            }
            else
            {
                // 路人，支持查看
                actionMenu = <Menu>{menu_open}</Menu>;
            } 
        }
        
        // console.log(this.state);
        console.log( item.files );
        
        
        return <li className={hiddenclass}>
            
            <UserAvatar data={item.user} className="avatarbox"/>

            <div className="feedbox">
                
                { (item.forward_is_paid > 0 || item.is_paid > 0 )  && <div className="paid"><Icon icon="dollar" color={Colors.LIGHT_GRAY3} title={t("此内容VIP订户可见")}/></div> }

                <div className="hovermenu" >
                <Popover {...this.props} content={actionMenu} position={Position.BOTTOM} interactionKind={PopoverInteractionKind.CLICK}>
                <Icon icon="chevron-down" title={t("更多操作")}/>
                </Popover>
                </div>
                
                <div className="userbox">
                    <div className="name"><UserLink data={item.user} /><span>@{item.user.username}</span></div>
                    <div className="time">
                    <Link to={"/feed/"+item.id}><MyTime date={item.timeline} /></Link>
                    {from} 
                    </div>
                </div>
                <div className="feedcontent">
                <FeedText text={item.text} more={t("显示更多")} less={<div className="top10">{t("↑收起")}</div>}/>

                { item.images && item.images.length > 0 && 
                    <ul className="photos">
                        { item.images.map( ( image ) => <li key={i++}>
                            {/* <a href={image.orignal_url} target="_blank"><img src={image.thumb_url} alt="cover"/></a> */}
                            { window.fowallet && window.fowallet.requestDownload ? <img src={image.thumb_url} alt="cover" className="thumb" onClick={()=>window.fowallet.requestDownload( {"fileUrl":image.orignal_url,"fileName":"cover.png"} , ()=>{ toast("已保存，请在「钱包」→「我的」→「我的下载」中分享到相册") } )}/> : <ModalImage small={image.thumb_url} large={image.orignal_url} className="thumb" /> }
                            
                        </li> 
                        )
                        }
                    </ul>
                }

                { item.files && item.files.length > 0 && <ul className="files">
                    { item.files.map( ( file ) => <li key={i++}>
                        
                    { window.fowallet && window.fowallet.requestDownload ? <Button text={file.name} onClick={()=>window.fowallet.requestDownload( {"fileUrl":file.url,"fileName":file.name} , ()=>{ toast("已保存，请在「钱包」→「我的」→「我的下载」中分享或打开") } )}/> : <Button text={file.name} onClick={()=>this.props.store.download( file.url , file.name )} icon="paperclip" minimal={true} large={true} /> }
                        
                        
                        {/* <a href={file.url} target="_blank"><Icon icon="paperclip" iconSize={18}/>{file.name}</a> */}
                    </li>
                    )
                    
                    }
                    
                    
                </ul>
                }
                </div>

                <div className="actionbar">
                    {/* <div className="share">
                        <LMIcon name="share" size={20} color={Colors.GRAY5} />{ item.share_count > 0 && <span>{item.share_count}</span> }
                    </div> */}
                    <div className="comment" onClick={()=>this.toggle_comment()}>
                        <LMIcon name="comment" size={20} color={Colors.GRAY5} />{ item.comment_count > 0 && <span>{item.comment_count}</span> }
                    </div>
                    <div className="up">
                        <LMIcon name="up" size={20} color={Colors.LIGHT_GRAY5} />{ item.up_count > 0 && <span>{item.up_count}</span> }
                    </div>
                    <div className="heart">
                        <LMIcon name="heart" size={20} color={Colors.LIGHT_GRAY5} />{ item.up_count > 0 && <span>{item.up_count}</span> }
                    </div>
                    {/* <div className="open">
                        <Icon icon="document-open" size={20} color={Colors.GRAY5} onClick={()=>window.open('/feed/'+item.id)} />
                    </div> */}
                
                </div>

                { this.state.show_comment && <div className="commentbox">
                { toInt(this.props.store.user.id) !== 0 && <Fragment>
                <div>
                    <TextArea className="pt-fill" value={this.state.commment_text} placeholder={t("请在这里输入评论，最长200字")} maxLength={200} onChange={(e)=>this.handleComment(e)}/>
                </div>
                <div>
                    <Button text={t("发送")} onClick={()=>this.sendComment()}/>
                </div>
                </Fragment>
                }
                { this.state.comments &&  <ul className="commentlist">
                {this.state.comments.map( (item) => <CommentItem data={item} key={item.id} admin={admin_uid} onRemove={()=>this.commentRemoved()} /> ) } 
                </ul>}
                {  this.state.comments.length < this.state.total && <div className="morelink" onClick={()=>this.loadComments()}>{t("载入更多")}</div>}
                
                </div>}
                
            </div>
        </li>  ;
    }
}