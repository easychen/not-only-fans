import React, { Component,Fragment } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import VisibilitySensor from 'react-visibility-sensor';
import { Callout , Intent, Spinner, NonIdealState } from "@blueprintjs/core";

import Cloumn3Layout from '../component/Cloumn3Layout';
import DocumentTitle from 'react-document-title';
import GroupCard from '../component/GroupCard';
import MemberItem from '../component/MemberItem'; 
import ActivityLink from '../util/ActivityLink';

import { toast , showApiError , inGroup, isApiOk } from '../util/Function';

@withRouter
@translate()
@inject("store")
@observer
export default class GroupMember extends Component
{
    state = {"group":{},"loaded":false,"members":[],"since_id":0,"loading":false};

    componentDidMount()
    {
       this.loadGroupInfo(); 
       this.loadMember( true );
    }

    async componentDidUpdate(prevProps) 
    {
        if (this.props.location !== prevProps.location) 
        {
            await this.loadMember( true , 0 );
        }
    }

    async loadMember( clean = false , sid = null )
    {
        const { t } = this.props;
        const gid = this.props.match.params.id;
        const since_id = sid === null ? this.state.since_id : sid;
        
        let filter = 'all';
        if( this.props.match.params.filter)
        {
            if( this.props.match.params.filter == 'blacklist' ) filter = 'blacklist';
            if( this.props.match.params.filter == 'contribute' ) filter = 'contribute';
            if( this.props.match.params.filter == 'comment' ) filter = 'comment';
        } 

        const { data } = await this.props.store.getGroupMember( gid , since_id , filter );
        this.setState({"loading":false});

        if( isApiOk( data ) )
        {
            if( data.data !=  undefined  )
            {
                if( !Array.isArray(data.data.members) ) data.data.members =[];
                
                let since_id_new = null;
                if( data.data.minid !=  null )
                {
                    const minid = parseInt( data.data.minid , 10 );
                    since_id_new = minid;
                }
                
                const newdata = clean ? data.data.members :this.state.members.concat(data.data.members);

                if( since_id_new === null )
                    this.setState({"members":newdata});
                else  
                    this.setState({"members":newdata,"since_id":since_id_new});  
            }
        }
        else showApiError( data , t );
    }

    async loadGroupInfo()
    {
        const { t } = this.props;
        const gid = this.props.match.params.id;
        
        if( parseInt( gid , 10  )> 0 )
        {
            const { data } = await this.props.store.getGroupDetail( gid );
            // console.log( data );
            if( isApiOk( data )  )
            {
                // ??????????????????
                if( parseInt( data.data.is_active , 10 ) === 0 )
                {
                    toast(t("??????????????????????????????"));
                    this.props.history.push("/groups");
                    return false;
                }
                else
                {
                    this.setState( { "group":data.data,"loaded":true } );
                }
                
            }else
                showApiError( data , t );   
        }
    }

    memberloading( visible )
    {
        if( visible )
        {
            // ???????????????????????????????????????????????????
            if( this.state.since_id != 0 )
            {
                this.setState({"loading":true});
                setTimeout(() => {
                    this.loadMember();
                }, 0);
            }
                
        }
        //console.log(e);
    }
    
    render()
    {
        const { t } = this.props;

        const is_member = this.props.store.user.groups && inGroup( this.state.group.id , this.props.store.user.groups ) ? true : false;

        const is_admin = this.props.store.user.admin_groups && inGroup( this.state.group.id , this.props.store.user.admin_groups ) ? true : false;

        let filter = 'all';
        if( this.props.match.params.filter)
        {
            if( this.props.match.params.filter == 'blacklist' ) filter = 'blacklist';
            if( this.props.match.params.filter == 'contribute' ) filter = 'contribute';
            if( this.props.match.params.filter == 'comment' ) filter = 'comment';
        } 

        const left = <div className="groupleft px10list">
        <GroupCard group={this.state.group}/></div>;

        const main = <div className="blocklist groupdetailbox">
        { !is_member && this.state.loaded && <div className="notmember">
            <Callout intent={Intent.PRIMARY} className="joincall">
            <p>{t("??????????????????????????????????????????????????????????????????")}</p>
            </Callout>
            </div>
        }

        <div className="feedfilter sticky">
            
            <div className="all">
            <ActivityLink label={t("??????")} to={"/group/member/"+this.state.group.id} activeOnlyWhenExact={true}/>
            </div>

            { is_admin && <Fragment>
                <div className="blacklist">
                    <ActivityLink label={t("???????????????")} to={"/group/member/blacklist/"+this.state.group.id} />
                </div>

                <div className="blacklist">
                    <ActivityLink label={t("???????????????")} to={"/group/member/contribute/"+this.state.group.id} />
                </div>

                <div className="blacklist">
                    <ActivityLink label={t("???????????????")} to={"/group/member/comment/"+this.state.group.id} />
                </div>
            </Fragment> }
            
            
            
        </div>

        { this.state.members.length > 0 && <div><ul className="memberlist">
            {this.state.members.map( (item) => <MemberItem data={item.user} key={item.id} is_admin={is_admin} filter={filter} group_id={this.state.group.id} /> ) } 
        </ul>
        { this.state.loading && <div className="hcenter"><Spinner intent={Intent.PRIMARY} small={true} /></div> }
        <VisibilitySensor onChange={(e)=>this.memberloading(e)}/>
        </div>
        }

        { this.state.members.length < 1 && <NonIdealState className="padding40"
                visual="search"
                title={t("????????????")}
                description={t("???????????????????????????")}
                
            /> }
       
        

        
        
    </div>;
        
        return <DocumentTitle title={t('??????')+'@'+t(this.props.store.appname)}><Cloumn3Layout left={left} main={main} /></DocumentTitle>;
    }
}