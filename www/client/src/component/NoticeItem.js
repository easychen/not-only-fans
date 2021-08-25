import React, { Component, Fragment } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import UserAvatar from './UserAvatar';
import UserLink from './UserLink';
import Linkify from 'react-linkify';
import MyTime from './MyTime';
import { Colors, Icon, Button } from '@blueprintjs/core';
import { toInt } from '../util/Function';
import SystemNoticeAction from '../component/SystemNoticeAction'; 


@withRouter
@translate()
@inject("store")
@observer
export default class NoticeItem extends Component
{
    state = { "notice" : null };

    componentDidMount()
    {
        if( this.props.data )
            this.setState( { "notice":this.props.data } );
    }

    imbox( uid )
    {
        this.props.store.openIm( uid );
    }
    
    render()
    {
        const { t } = this.props;
        const item = this.state.notice;
        if( item )
        {
            if( item.from_uid == 0  ) item.from = {"uid":0};
            if( item.uid == 0  ) item.user = {"uid":0};
            
            item.other = item.uid === item.from_uid  ? item.to_uid : item.from_uid;

            item.class = toInt(item.is_read) === 1 ? 'read':'unread';
              
        } 

        

        return item && <li className={item.class}>
        { item.uid === item.from_uid && 
        <div className="self item">
            <UserAvatar data={item.to} className="avatar"/>
            
            <div className="content">
                <div className="user"><UserLink data={item.to} /> <span className="explain">{item.to.username} · <MyTime date={item.timeline}/></span> </div>
                <div className="text">
        
                <Icon icon="double-chevron-left" iconSize={12} color={Colors.GRAY5} />&nbsp;
                <Linkify properties={{target: '_blank'}}>{item.text}</Linkify>
            
                </div>
            </div>
            
            <div className="action">
                <Button text={t("查看对话")} onClick={()=>this.imbox( item.other )} minimal={true} />
            </div>
            
        
        </div> }  
        { item.uid !== item.from_uid && <div className="others item">
            {item.from.uid !== 0 && <UserAvatar data={item.from} className="avatar"/>}
            
            <div className="content">
                <div className="user">
                
                <UserLink data={item.from} />
                
                {toInt(item.is_read) === 0 && <Icon icon="dot" iconSize={9} color={Colors.BLUE5} /> } 
                
                <span className="explain">{item.from.username} · <MyTime date={item.timeline}/></span> 
                
                </div>
                <div className="text">
        
                    { item.from.uid !== 0 &&  <Fragment><Icon icon="double-chevron-right" iconSize={12} color={Colors.GRAY5} className="right5" /><Linkify properties={{target: '_blank'}}>{item.text}</Linkify></Fragment> }

                    { item.from.uid === 0 && <SystemNoticeAction data={JSON.parse(item.text)} />}


                    
            
                </div>
            </div>

            { item.from.uid !== 0 &&  <div className="action">
                <Button text={t("查看对话")} onClick={()=>this.imbox( item.other )} minimal={true} />
            </div>}

            { item.from.uid === 0 &&  <div className="action">
                <Button text={t("查看通知")} onClick={()=>this.imbox( item.other )} minimal={true} />
            </div>}
        
        </div> }  
        </li>;
    }
}