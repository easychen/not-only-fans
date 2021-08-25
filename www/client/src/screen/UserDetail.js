import React, { Component,Fragment } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import { Intent, Spinner, NonIdealState, ButtonGroup, Button } from "@blueprintjs/core";

import Cloumn3Layout from '../component/Cloumn3Layout';
import UserCard from '../component/UserCard';
import { isApiOk, showApiError, toInt } from '../util/Function';

import FeedItem from '../component/FeedItem'; 
import ActivityLink from '../util/ActivityLink';
import VisibilitySensor from 'react-visibility-sensor';
import DocumentTitle from 'react-document-title';
import BlacklistButton from '../component/BlacklistButton'; 


@withRouter
@translate()
@inject("store")
@observer
export default class UserDetail extends Component
{
    state = {"user":null,"loaded":false,"feeds":null,"loading":false};

    componentDidMount()
    {
        if( !this.props.match.params.id ) 
            this.props.history.replace('/user/'+this.props.store.user.uid);
        else
        {
            this.loadUserInfo(); 
            this.loadUserFeed( true );
        }
    
    }

    async componentDidUpdate(prevProps) 
    {
        if (this.props.location !== prevProps.location) 
        {
            await this.loadUserFeed( true , 0 );
        }
    }

    async loadUserInfo()
    {
        const { t } = this.props;
        const uid = this.props.match.params.id ? this.props.match.params.id : this.props.store.user.uid;
        const { data } = await this.props.store.getUserDetail( uid );
        if( isApiOk( data ) )
        {
            this.setState( {"user":data.data} );
        }
        else
        {
            showApiError( data , t );
            this.props.history.push('/');
        }
    }

    async loadUserFeed( clean = false , sid = null )
    {
        const { t } = this.props;
        const uid = this.props.match.params.id ? this.props.match.params.id : this.props.store.user.uid;
        const since_id = sid === null ? this.state.since_id : sid;
        
        let filter = 'all';
        if( this.props.match.params.filter)
        {
            if( this.props.match.params.filter == 'paid' ) filter = 'paid';
            if( this.props.match.params.filter == 'media' ) filter = 'media';
        } 


        const { data } = await this.props.store.getUserFeed( uid , since_id , filter );
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
        else
        {
            showApiError( data , t );
        }
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
                    this.loadUserFeed();
                }, 0);
            }
                
        }
        //console.log(e);
    }

    im( uid )
    {
        this.props.store.openIm( uid );
    }
    
    
    render()
    {
        const uid = this.props.match.params.id ? this.props.match.params.id : this.props.store.user.uid;
        const is_me = toInt( uid ) === toInt( this.props.store.user.uid ); 
        const { t } = this.props;
        const title = ( this.state.user && this.state.user.nickname ) ? this.state.user.nickname + t("@"+this.props.store.appname) : t("@"+this.props.store.appname);

        const main = <div className="blocklist groupdetailbox"> 
        <div className="feedfilter">
            
            <div className="all">
            <ActivityLink label={t("全部")} to={"/user/"+uid} activeOnlyWhenExact={true}/>
            </div>
            {/* <div className="free">
                <ActivityLink label={t("免费")} to="/free" />
            </div> */}
            
            { is_me && <div className="paid">
                <ActivityLink label={t("付费")} to={"/user/paid/"+uid} />
            </div> }
            
            <div className="media">
            <ActivityLink label={t("图片")} to={"/user/media/"+uid} />
            </div>
        </div>

        { ( this.state.feeds && Array.isArray( this.state.feeds ) && this.state.feeds.length > 0 ) && <div><ul className="feedlist">
            {this.state.feeds.map( (item) => <FeedItem data={item} key={item.id}/> ) } 
        </ul>
        { this.state.loading && <div className="hcenter"><Spinner intent={Intent.PRIMARY} small={true} /></div> }
        <VisibilitySensor onChange={(e)=>this.feedloading(e)}/>
        </div>
        }

        { !( this.state.feeds && Array.isArray( this.state.feeds ) && this.state.feeds.length > 0 ) &&  <NonIdealState className="padding40"
                    visual="search"
                    title={t("还没有内容")}
                    description={t("没有符合条件的内容")}
                    
                /> }
       
        

        
        
        </div>;

        const left= <Fragment>
            <UserCard user={this.state.user}/>
            { !is_me && <ButtonGroup className="blacklistbuttonbox hcenter"><Button text={t("私信")} intent={Intent.PRIMARY} icon="chat" onClick={()=>this.im(uid)}/><BlacklistButton uid={uid}/></ButtonGroup> }
        </Fragment>;

        return <DocumentTitle title={title}><Cloumn3Layout left={left} main={main} /></DocumentTitle>;
    }
}