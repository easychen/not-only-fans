import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter, Link } from 'react-router-dom';
import { translate } from 'react-i18next';

import { toast , showApiError , inGroup, isApiOk, toInt } from '../util/Function';
import { Button, ButtonGroup, Callout , Intent, Spinner, NonIdealState } from "@blueprintjs/core";


import Cloumn3Layout from '../component/Cloumn3Layout';
import UserCard from '../component/UserCard';
import PublishBox from '../component/PublishBox';
import FeedItem from '../component/FeedItem'; 
import ActivityLink from '../util/ActivityLink';
import VisibilitySensor from 'react-visibility-sensor';
import DocumentTitle from 'react-document-title';

@translate()
@inject("store")
@withRouter
@observer
export default class Home extends Component
{
    state = {"loaded":false,"feeds":[],"since_id":0,"loading":false,"maxid":0,"show_has_new":false};

    componentDidMount()
    {
       this.loadFeed( true );
       this.check = setInterval( ()=>this.getLastId() , 1000*60 );
    }

    componentWillUnmount()
    {
        clearInterval( this.check );
    }
    
    async componentDidUpdate(prevProps) 
    {
        if (this.props.location !== prevProps.location) 
        {
            await this.loadFeed( true , 0 );
        }
    }

    async getLastId()
    {
        const { t } = this.props;
        let filter = 'all';
        if( this.props.match.params.filter)
        {
            if( this.props.match.params.filter == 'paid' ) filter = 'paid';
            if( this.props.match.params.filter == 'media' ) filter = 'media';
        }
        const { data } = await this.props.store.getTimelineLastId( filter );
        if( isApiOk( data ) )
        {
            const lastid = toInt( data.data );
            if( lastid > this.state.maxid )
                this.setState({"show_has_new":true});
            else
                this.setState({"show_has_new":false});
        }else
            showApiError( data , t );
    }
    

    async loadFeed( clean = false , sid = null )
    {
        const { t } = this.props;
        const since_id = sid === null ? this.state.since_id : sid;
        
        let filter = 'all';
        if( this.props.match.params.filter)
        {
            if( this.props.match.params.filter == 'paid' ) filter = 'paid';
            if( this.props.match.params.filter == 'media' ) filter = 'media';
        } 

        const { data } = await this.props.store.getHomeTimeline( since_id , filter );
        this.setState({"loading":false,"show_has_new":false,"loaded":true});
        
        if( isApiOk( data ) )
        {
            if( data.data !=  undefined  )
            {
                if( !Array.isArray(data.data.feeds) ) data.data.feeds =[];
                
                let since_id_new = null;
                if( data.data.minid !=  null )
                {
                    const minid = parseInt( data.data.minid , 10 );
                    since_id_new = minid;
                }

                let maxid = this.state.maxid;
                if( data.data.maxid !=  null )
                {
                    if( toInt( data.data.maxid ) > toInt( maxid ) )
                        maxid = toInt( data.data.maxid );
                }
                
                const newdata = clean ? data.data.feeds :this.state.feeds.concat(data.data.feeds);

                if( since_id_new === null )
                    this.setState({"feeds":newdata,"maxid":maxid});
                else  
                    this.setState({"feeds":newdata,"since_id":since_id_new,"maxid":maxid});  
            }
        }
        else showApiError( data , t );
    }

    feedloading( visible )
    {
        if( visible )
        {
            // ???????????????????????????????????????????????????
            if( this.state.since_id != 0 )
            {
                this.setState({"loading":true});
                setTimeout(() => {
                    this.loadFeed();
                }, 0);
            }
                
        }
        //console.log(e);
    }

    // ?????????????????????
    published()
    {
        // ????????????id
        this.getLastId();

        // ?????????????????????????????????????????????????????????
        this.props.store.updateUserInfo();
        
    }

    reload()
    {
        this.setState({"show_has_new":false});
        this.loadFeed( true , 0 );
    }

    render()
    {   
        const { t } = this.props;
        const { user } = this.props.store;
        
        const main = <div className="px10list">{user.group_count > 0 && <PublishBox groups={user.groups} onFinish={()=>this.published()}/>}

        <div className="feedfilter sticky">
        <div className="all">
        <ActivityLink label={t("??????")} to={"/"} activeOnlyWhenExact={true}/>
        </div>
        <div className="paid">
            <ActivityLink label={t("??????")} to={"/home/paid/"} />
        </div>
        <div className="media">
        <ActivityLink label={t("??????")} to={"/home/media/"} />
        </div>
    </div>

    { this.state.show_has_new && <div className="hasnewfeed" onClick={()=>this.reload()}>{t("??????????????????????????????")}</div> }

    { this.state.feeds.length > 0 && <div><ul className="feedlist">
        {this.state.feeds.map( (item) => <FeedItem data={item} key={item.id} /> ) } 
    </ul>
    { this.state.loading && <div className="hcenter"><Spinner intent={Intent.PRIMARY} small={true} /></div> }
    <VisibilitySensor onChange={(e)=>this.feedloading(e)}/>
    </div>
    }

    { this.state.feeds.length < 1 && <NonIdealState className="padding40"
            visual="search"
            title={t("???????????????")}
            description={t("??????????????????????????????????????????????????????~")}
            children={<div className="top50"><Button icon="flame" large={true} onClick={()=>this.props.history.push("/group")} text={t("??????????????????")}/></div>}
        /> }
        
        </div>;

        
        const page = this.state.loaded ? <DocumentTitle title={t("??????")+'@'+t(this.props.store.appname)}><Cloumn3Layout  left={<UserCard/>} main={main} /></DocumentTitle> : null;
        return page;
    }
}