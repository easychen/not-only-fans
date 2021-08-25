import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';

import Cloumn3Layout from '../component/Cloumn3Layout';
import UserCard from '../component/UserCard';
import DocumentTitle from 'react-document-title';
import { isApiOk, showApiError } from '../util/Function';

import ContributeItem from '../component/ContributeItem'; 
import ActivityLink from '../util/ActivityLink';
import VisibilitySensor from 'react-visibility-sensor';
import { Intent, Spinner, Button, NonIdealState } from "@blueprintjs/core";

@withRouter
@translate()
@inject("store")
@observer
export default class GroupContribute extends Component
{
    state = {"loaded":false,"feeds":[],"since_id":0,"loading":false};

    componentDidMount()
    {
       this.loadFeed( true );
    }

    async componentDidUpdate(prevProps) 
    {
        if (this.props.location !== prevProps.location) 
        {
            await this.loadFeed( true , 0 );
        }
    }

    async loadFeed( clean = false , sid = null )
    {
        const { t } = this.props;
        const gid = this.props.match.params.id;
        const since_id = sid === null ? this.state.since_id : sid;
        
        let filter = 'all';
        if( this.props.match.params.filter)
        {
            if( this.props.match.params.filter == 'todo' ) filter = 'todo';
            if( this.props.match.params.filter == 'allow' ) filter = 'allow';
            if( this.props.match.params.filter == 'deny' ) filter = 'deny';
        } 

        const { data } = await this.props.store.getContribute( since_id , filter );
        this.setState({"loading":false});

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
                
                const newdata = clean ? data.data.feeds :this.state.feeds.concat(data.data.feeds);

                if( since_id_new === null )
                    this.setState({"feeds":newdata});
                else  
                    this.setState({"feeds":newdata,"since_id":since_id_new});  
            }
        }
        else showApiError( data , t );
    }

    feedloading( visible )
    {
        if( visible )
        {
            // 发生变动且能看到底部，进行数据加载
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

    
    render()
    {
        const { t } = this.props;
        const main = <div className="blocklist groupdetailbox"> 
        <div className="feedfilter sticky">
            
            
            {/* <div className="free">
                <ActivityLink label={t("VIP")} to="/free" />
            </div> */}
            <div className="todo">
                <ActivityLink label={t("待审")} to={"/group/contribute/todo"} />
            </div>
            <div className="media">
            <ActivityLink label={t("通过")} to={"/group/contribute/allow"} />
            </div>
            <div className="media">
            <ActivityLink label={t("拒稿")} to={"/group/contribute/deny"} />
            </div>
            <div className="all">
            <ActivityLink label={t("全部")} to={"/group/contribute/"} activeOnlyWhenExact={true}/>
            </div>

            {/* <div className="button"><Button text={t("保存审核结果")}  small={true} intent={Intent.PRIMARY} onClick={()=>this.save()}/></div> */}
        </div>

        { ( this.state.feeds && Array.isArray( this.state.feeds ) && this.state.feeds.length > 0 ) && <div><ul className="feedlist">
            {this.state.feeds.map( (item) => <ContributeItem data={item.feed} key={item.id} fid={item.feed_id} /> ) } 
        </ul>
        { this.state.loading && <div className="hcenter"><Spinner intent={Intent.PRIMARY} small={true} /></div> }
        <VisibilitySensor onChange={(e)=>this.feedloading(e)}/>
        </div>
        }

        { !( this.state.feeds && Array.isArray( this.state.feeds ) && this.state.feeds.length > 0 ) && <NonIdealState className="padding40"
                    visual="search"
                    title={t("还没有内容")}
                    description={t("没有符合条件的内容")}
                    
                /> }
       
        </div>;

        return <DocumentTitle title={t("投稿管理@")+this.props.store.appname}><Cloumn3Layout left={<UserCard/>} main={main} /></DocumentTitle>;
    }
}