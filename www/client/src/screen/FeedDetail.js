import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';

import Cloumn3Layout from '../component/Cloumn3Layout';
import DocumentTitle from 'react-document-title';
import { toast, isApiOk, showApiError } from '../util/Function';
import FeedItem from '../component/FeedItem';
import {  Button } from "@blueprintjs/core";
import BackButton from '../component/BackButton'; 

@withRouter
@translate()
@inject("store")
@observer
export default class FeedDetail extends Component
{
    state = {"feed":null};
    async componentDidMount()
    {
        const { t } = this.props;
        const id = this.props.match.params.id ? parseInt( this.props.match.params.id , 10 ) : 0 ;

        if( id < 1 )
        {
            toast(t("无法获取内容ID，将转向到首页"));
            this.props.history.replace('/');
        }

        const { data } = await this.props.store.getFeedDetail( id );
        if( isApiOk( data ) )
        {
            this.setState( {"feed":data.data} );
        }
        else
            showApiError( data , t );
    }
    
    render()
    {
        console.log( this.props.history , window.document.referrer );
        
        const { t } = this.props;
        const item = this.state.feed;
        const main = item ? <div className="feeddetail">
            <BackButton/>
            
            <ul className="feedlist"><FeedItem data={item} key={item.id} show_comment={true}/></ul></div>:'';
        return <DocumentTitle title={this.props.t(this.props.store.appname)}><Cloumn3Layout  main={main} /></DocumentTitle>;
    }
}