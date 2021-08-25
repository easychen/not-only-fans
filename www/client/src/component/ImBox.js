import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import RND from 'react-rnd';
import { isApiOk, showApiError, toInt } from '../util/Function';
import { Icon, Colors } from '@blueprintjs/core';
import UserAvatar from './UserAvatar';
import UserLink from './UserLink';
import MessageItem from '../component/MessageItem'; 
import Sockette from 'sockette';
@withRouter
@translate()
@inject("store")
@observer
export default class ImBox extends Component
{
    
    state = {"user":null,"text":'',"messages":[],"since_id":0,"total":0,"wsconnect":false,"maxid":0};
    
    componentDidMount()
    {
        // if( this.state.user && toInt( this.state.user.id ) == toInt( this.props.store.im_to_uid )  )
        // {

        // }
        // else
        // {
            
        // }
        this.loadUser();
        this.loadMessages( true , true , 0  );

        // 连接websocket
        this.connect();
        // 加上一分钟一次的慢更新，作为没有双方在线时的补救
        this.check = setInterval( ()=>this.checkNew() , 1000*60 );
    }

    componentWillUnmount()
    {
        // 关闭websocket
        clearInterval( this.check );
        if( this.ws )
        {
            // this.setState({"wsconnect":false})};
            this.ws.close();
        }
            
    }

    connect()
    {
        this.ws = new Sockette( process.env.REACT_APP_WEBSOCKECT +'?uid='+this.props.store.user.id+'&to_uid='+this.props.store.im_to_uid, {
            timeout: 5e3,
            maxAttempts: 10,
            onopen: e => { this.setState({"wsconnect":true}) },
            onmessage: e => { this.receive( e )},
            onreconnect: e => {this.setState({"wsconnect":false})},
            onmaximum: e => console.log('Stop Attempting!', e),
            onerror: e => console.log('Error:', e)
          });
    }

    receive( e )
    {
        if( e.data && e.data === 'updated' )
        {
            this.loadMessages( true , true , 0 );
        }
    }

    async checkNew()
    {
        const { data } = await this.props.store.getMessageLatestId( this.props.store.im_to_uid );

        if( isApiOk( data ) )
        {
            if( data.data && toInt( data.data ) > 0 )
            {
                if( toInt( this.state.maxid ) > 0 &&  toInt( data.data ) > toInt( this.state.maxid ) )
                {
                   this.loadMessages( true , true , 0 );
                }
            }
        }
        else
            showApiError( data , this.props.t );

    } 

    async loadUser()
    {
        if( this.props.store.im_to_uid > 0 )
        {
            const { data } = await this.props.store.getUserDetail( this.props.store.im_to_uid );
            if( isApiOk( data ) )
            {
                this.setState({"user":data.data});
            }
            else
                showApiError( data , this.props.t );
        }

        if( toInt( this.props.store.im_to_uid )  === 0 )
        {
            this.setState({"user":{"uid":0}});
        }
        
    }

    move( e , d )
    {
        // { this.setState({ x: d.x, y: d.y }) }
        this.props.store.im_position = d;
    }

    scrollToBottom = () => 
    {
        this.messagesEnd.scrollIntoView({ behavior: "smooth" });
    }

    changed( e , name )
    {
        let o = {};
        o[name] =  e.target.value ;
        this.setState( o ) ;
    }

    async loadMessages( tobottom= false , clean = false , sid = null  )
    {
        const { t } = this.props;
        const since_id = sid === null ? this.state.since_id : sid;
        const { data } = await this.props.store.getMessageHistory( this.props.store.im_to_uid , since_id );

        if( isApiOk( data ) )
        {
            if( data.data !=  undefined  )
            {
                if( !Array.isArray(data.data.messages) ) data.data.messages =[];

                data.data.messages = data.data.messages.reverse();
                
                let since_id_new = null;
                if( data.data.minid !=  null )
                {
                    const minid = parseInt( data.data.minid , 10 );
                    since_id_new = minid;
                }

                if( data.data.maxid != null )
                {
                    if( toInt( data.data.maxid ) > toInt( this.state.maxid ) )
                        this.setState( {"maxid":toInt( data.data.maxid )} );
                }
                
                const newdata = clean ? data.data.messages :data.data.messages.concat(this.state.messages);

                if( since_id_new === null )
                    this.setState({"messages":newdata,"total":toInt(data.data.total)});
                else  
                    this.setState({"messages":newdata,"since_id":since_id_new,"total":toInt(data.data.total)});
                    
                if( this.messagesEnd && tobottom )
                    this.scrollToBottom();  
                
            }
        }
        else showApiError( data , t );
    }

    async send()
    {
        const { t } = this.props;
        const text = this.state.text;
        if( text.length > 0 && this.props.store.im_to_uid > -1 )
        {
            const { data } = await this.props.store.sendMessage( this.props.store.im_to_uid , text );
            if( isApiOk( data ) )
            {
                this.loadMessages( true , true , 0 );
                this.setState({"text":''});
                if( this.ws )
                {
                    const watchkey = toInt( this.props.store.user.id ) < toInt( this.props.store.im_to_uid ) ? toInt( this.props.store.user.id ) + '-' +toInt( this.props.store.im_to_uid ) : toInt( this.props.store.im_to_uid ) + '-' +toInt( this.props.store.user.id );

                    this.ws.send("refresh:"+watchkey);
                } 
                // 如果 websocket 已经连接，发送更新信号
                // 这样另外一边就可以刷新了
            }else
                showApiError( data , t );
        }

        return false;
    }

    close()
    {
        this.props.store.im_open = false;
        this.props.store.im_to_uid = -1;
        // this.setState({"text":''});
    }

    render()
    {
        const { t } = this.props;
        const user = this.state.user;
        return user && <RND className="imbox" 
        size={{ width: 400,  height: 500 }}
        position={this.props.store.im_position}
        onDragStop={(e, d) => this.move( e , d )}
        dragHandleClassName="topbar"
        >
            <div className="topbar">
                <div className="title">
                <UserAvatar data={user} className="avatar"/><UserLink data={user} className="left5"/>
                
                { this.state.wsconnect && <div className="left5"><Icon icon="updated" iconSize={12}  title={t("自动更新")} color={Colors.BLUE2} className="left5 vcenter"/></div> }

                { !this.state.wsconnect && <div className="left5"><Icon icon="refresh" title={t("手动刷新")} iconSize={12} color={Colors.GRAY5} className="left5 vcenter pointer" onClick={()=>this.loadMessages(true,true,0)}/></div> }


                </div>
                <div className="icon">
                    <Icon icon="small-cross" onClick={()=>this.close()} className="pointer"/>
                </div>
            </div>
            <div className="chat">
            {  this.state.messages.length < this.state.total && <div className="morelink" onClick={()=>this.loadMessages(false)}>{t("- 载入更多 -")}</div>}
             
            { this.state.messages && this.state.messages.map( (item) => <MessageItem data={item} key={item.id} /> ) }
            <div style={{ float:"left", clear: "both" }}
                ref={(el) => { this.messagesEnd = el; }}>
            </div>
            </div>
            <div className="sendform">
            <form onSubmit={()=>this.send()} method="post" action="javascript:void(0)">
            <input id="text" type="text" placeholder={t("输入文字，按回车键↩发送")}  className="pt-fill" value={this.state.text} onChange={e=>this.changed(e,"text")} autoComplete="off" disabled={this.state.user.uid === 0} />
            </form>
            </div>
        </RND>;
    }
}