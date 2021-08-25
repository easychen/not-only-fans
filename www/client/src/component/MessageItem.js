import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import MyTime from './MyTime';
import Linkify from 'react-linkify';
import SystemNoticeAction from '../component/SystemNoticeAction';
import { toInt } from '../util/Function';

@withRouter
@translate()
@inject("store")
@observer
export default class MessageItem extends Component
{
    state = {"message":null};

    componentDidMount()
    {
        if( this.props.data )
            this.setState({"message":this.props.data}); 
    }
    
    render()
    {
        const item = this.state.message;
        if( !item ) return null;

        // console.log( 'item' , item );

        const classname = item.uid == item.from_uid ? 'line right':'line left'
        return <div className="messageitem">
            <div className={classname}>
                
                
                
                { toInt( item.from_uid )  !== 0 && <div className="text"><Linkify properties={{target: '_blank'}}>{item.text}</Linkify></div>}
                
                { toInt( item.from_uid )  === 0 && <div className="text"><SystemNoticeAction data={JSON.parse(item.text)} /></div>}
                
                
                
                <div className="explain"><MyTime date={item.timeline}/></div>
            </div>
            
        </div>;
    }
}