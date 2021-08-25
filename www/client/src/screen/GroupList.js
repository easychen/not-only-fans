import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import Web3 from 'web3';
import Cloumn3Layout from '../component/Cloumn3Layout';
import UserCard from '../component/UserCard';
import { isApiOk, showApiError,toast } from '../util/Function';
import GroupListItem from '../component/GroupListItem'; 
import { Icon, Button } from "@blueprintjs/core";
import DocumentTitle from 'react-document-title';

@withRouter
@translate()
@inject("store")
@observer
export default class GroupList extends Component
{
    state = { "groups":[], "mygroups":[] };

    async componentDidMount()
    {
        if( !Web3.givenProvider )
       {
           toast(this.props.t("请先安装MetaMask等插件"));
       }else
       {
        this.web3 = new Web3(Web3.givenProvider);
       } 
       
        await this.loadMyGroups();
        await this.loadGroups();
    }

    async loadGroups()
    {
        const { data } = await this.props.store.getGroupTop100();
        if( isApiOk( data ) )
        {
            if( data.data && Array.isArray( data.data ))
            {
                const mygroupids = this.state.mygroups.map(  item => item.id );

                this.setState( {"groups":data.data.filter( item => !mygroupids.includes( item.id ) )} );

            }
                
        }
        else
            showApiError( data , this.props.t );

    }

    async loadMyGroups()
    {
        const { data } = await this.props.store.getGroupMine();
        if( isApiOk( data ) )
        {
            if( data.data && Array.isArray( data.data ))
                this.setState( {"mygroups":data.data} );
        }
        else
            showApiError( data , this.props.t );
    }

    render()
    {
        const { t } = this.props;
        const groups = this.state.groups;
        const mygroups = this.state.mygroups;
        const main = <div className="blocklist">
        <div className="createnotice whitebox">
            <div className="left">
                {t("创建自己的栏目，分享价值并赚取ETH")}
            </div>
            <div className="right">
                <div className="wide">
                    <Button onClick={()=>this.props.history.push("/group/create")} icon="plus" large={true} minimal={true} text={t("创建")}/>
                </div>
                <div className="narrow">
                    <Button onClick={()=>this.props.history.push("/group/create")} icon="plus" large={true} />
                </div>
                
            </div>
        </div>

        { mygroups && mygroups.length>0 && <ul className="grouplist">
            {mygroups.map( (item) => <GroupListItem data={item} key={item.id}/> ) }
        </ul> }

        { groups && groups.length>0 && <ul className="grouplist">
            {groups.map( (item) => <GroupListItem data={item} key={item.id}/> ) }
        </ul> }
        </div>;
        return <DocumentTitle title={t("栏目Top100")+'@'+t(this.props.store.appname)}><Cloumn3Layout left={<UserCard/>} main={main} /></DocumentTitle>;
    }
}