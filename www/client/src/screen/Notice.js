import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';

import Cloumn3Layout from '../component/Cloumn3Layout';
import UserCard from '../component/UserCard';
import DocumentTitle from 'react-document-title';
import NoticeItem from '../component/NoticeItem'; 
import { toInt, isApiOk, showApiError } from '../util/Function';
import VisibilitySensor from 'react-visibility-sensor';
import { Intent, Spinner, NonIdealState } from '@blueprintjs/core';


@withRouter
@translate()
@inject("store")
@observer
export default class Notice extends Component
{
    state = { "messages":[],"loading":false,"since_id":0,"total":0 };

    componentDidMount()
    {
        this.loadMessageGroupList();
    }

    async loadMessageGroupList( clean = false , sid = null  )
    {
        const { t } = this.props;
        const since_id = sid === null ? this.state.since_id : sid;
        const { data } = await this.props.store.getMessageGroupList( since_id );
        this.setState({"loading":false});
        if( isApiOk( data ) )
        {
            if( data.data !=  undefined  )
            {
                if( !Array.isArray(data.data.messages) ) data.data.messages =[];

                // data.data.messages = data.data.messages.reverse();
                
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
                
                const newdata = clean ? data.data.messages :this.state.messages.concat(data.data.messages);

                if( since_id_new === null )
                    this.setState({"messages":newdata,"total":toInt(data.data.total)});
                else  
                    this.setState({"messages":newdata,"since_id":since_id_new,"total":toInt(data.data.total)});
                
            }
        }
        else showApiError( data , t );

    }

    messageloading( visible )
    {
        if( visible )
        {
            // 发生变动且能看到底部，进行数据加载
            if( this.state.since_id != 0 )
            {
                this.setState({"loading":true});
                setTimeout(() => {
                    this.loadMessageGroupList();
                }, 0);
            }
                
        }
    }
    
    
    render()
    {
        const { t } = this.props;
        const messages = this.state.messages;
        const main = <div className="noticebox">
        { messages.length > 0 && Array.isArray( messages ) && <ul className="noticelist">
            { messages.map( (item) => <NoticeItem key={item.id}  data={item}/> ) }
        </ul> }
        { messages.length < 1 &&  <NonIdealState className="padding40"
                    visual="chat"
                    title={t("没有消息")}
                    description={t("消息箱里很安静")}
                    
                />}
        { this.state.loading && <div className="hcenter"><Spinner intent={Intent.PRIMARY} small={true} /></div> }
            <VisibilitySensor onChange={(e)=>this.messageloading(e)}/>
        </div>;
        return <DocumentTitle title={t('消息')+'@'+t(this.props.store.appname)}><Cloumn3Layout left={<UserCard/>} main={main}/></DocumentTitle>;
    }
}