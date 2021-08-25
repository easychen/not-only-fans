import React, { Component, Fragment } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import { toast , showApiError , inGroup, isApiOk } from '../util/Function';
import { Button, ButtonGroup, Callout , Intent, Spinner, NonIdealState } from "@blueprintjs/core";
import BackButton from '../component/BackButton'; 

import Web3 from 'web3';

import Cloumn3Layout from '../component/Cloumn3Layout';
import GroupCard from '../component/GroupCard';
// import BuyVipButtonFo from '../component/BuyVipButtonFo';
import BuyVipButton from '../component/BuyVipButton';
import FeedItem from '../component/FeedItem'; 
import ActivityLink from '../util/ActivityLink';
import VisibilitySensor from 'react-visibility-sensor';
import DocumentTitle from 'react-document-title';


@translate()
@inject("store")
@withRouter
@observer
export default class GroupDetail extends Component
{
    state = {"group":{},"loaded":false,"feeds":[],"since_id":0,"loading":false,"paid_feed_count":0};

    componentDidMount()
    {
       this.loadGroupInfo(); 
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
        const web3 = new Web3(Web3.givenProvider);
        const { t } = this.props;
        const gid = this.props.match.params.id;
        const since_id = sid === null ? this.state.since_id : sid;
        
        let filter = 'all';
        if( this.props.match.params.filter)
        {
            if( this.props.match.params.filter == 'paid' ) filter = 'paid';
            if( this.props.match.params.filter == 'media' ) filter = 'media';
        } 

        const { data } = await this.props.store.getGroupFeed( gid , since_id , filter );
        this.setState({"loading":false});

        if( isApiOk( data ) )
        {
            if( data.data !=  undefined  )
            {
                
                // if( data.data.topfeed &&  data.data.topfeed.files )
                // {
                //     const  fdata = JSON.parse( data.data.topfeed.files );
                //     if( fdata.url && fdata.name  )
                //         data.data.topfeed.files =  [fdata];
                //     else
                //         data.data.topfeed.files = false;
                // }
                
                if( data.data.topfeed && data.data.feeds )
                {
                    data.data.feeds = data.data.feeds.filter( item => item.id != data.data.topfeed.id );
                }
                
                if( !Array.isArray(data.data.feeds) ) data.data.feeds =[];
                
                let since_id_new = null;
                if( data.data.minid !=  null )
                {
                    const minid = parseInt( data.data.minid , 10 );
                    since_id_new = minid;
                }
                
                const newdata = clean ? data.data.feeds :this.state.feeds.concat(data.data.feeds);

                if( since_id_new === null )
                    this.setState({"feeds":newdata,"topfeed":data.data.topfeed,"paid_feed_count":data.data.paid_feed_count});
                else  
                    this.setState({"feeds":newdata,"topfeed":data.data.topfeed,"since_id":since_id_new,"paid_feed_count":data.data.paid_feed_count});  
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
            console.log( data );
            if( !showApiError( data , t )  )
            {
                // 检查开启状态
                if( parseInt( data.data.is_active , 10 ) == 0 )
                {
                    toast(t("栏目不存在或已被关闭"));
                    this.props.history.push("/groups");
                    return false;
                }
                else
                {
                    this.setState( { "group":data.data,"loaded":true } );
                }
                
            }   
        }
    }

    async join( groupid )
    {
        // alert(groupid);
        const { t } = this.props;
        const { data } = await this.props.store.joinGroup( groupid );
        if( !showApiError( data , t )  )
        {
            const { data } = await this.props.store.updateUserInfo();
            if( !showApiError( data , t )  )
            {
                toast(t("您已成功订阅栏目"));
                this.loadGroupInfo();
                this.loadFeed( true , 0 );
            }
            //this.props.store.user.group_count ++;
            //window.location.reload();
        } 
    }

    async quit( groupid )
    {
        const { t } = this.props;
        
        if( window.confirm( t("确定要取消订阅吗？退出后VIP订户需要重新购买哦~😯") ) )
        {
            const { data } = await this.props.store.quitGroup( groupid );
            if( !showApiError( data , t )  )
            {
                const { data } = await this.props.store.updateUserInfo();
                if( !showApiError( data , t )  )
                {
                    toast(t("您已成功取消订阅"));
                    this.loadGroupInfo();
                }
            } 
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
                    this.loadFeed();
                }, 0);
            }
                
        }
        //console.log(e);
    }

    contribute()
    {
        this.props.history.push('/group/contribute/todo');
    }

    member()
    {
        this.props.history.push('/group/member/'+this.state.group.id);
    }

    settings()
    {
        this.props.history.push('/group/settings/'+this.state.group.id);
    }

    render()
    {
        const { t } = this.props;
        const web3 = new Web3(Web3.givenProvider);
        const is_member = this.props.store.user.groups && inGroup( this.state.group.id , this.props.store.user.groups );

        const is_admin = this.props.store.user.admin_groups && inGroup( this.state.group.id , this.props.store.user.admin_groups ) ;

        const is_self = this.state.group &&  this.state.group.author_uid == this.props.store.user.id;

        
        const main = <div className="blocklist groupdetailbox">

            <BackButton/>
            
            {/* { !is_member && this.state.loaded ?  <div className="notmember">
                <Callout intent={Intent.PRIMARY} className="joincall">
                <p>{t("你不是栏目订户，只有加入后才能查看栏目内容。")}</p>
                </Callout>
                </div>
            :*/}
            { this.state.loaded && <Fragment>{ this.state.topfeed && <div><ul className="feedlist top">
            <FeedItem data={this.state.topfeed} key={this.state.topfeed.id} in_group={true}/> 
            </ul></div>
            }

            <div className="feedfilter sticky">
                {/* <div className="hot">
                <ActivityLink label={t("热门")} to="/hot" activeOnlyWhenExact={true}/>
                </div> */}
                <div className="all">
                <ActivityLink label={t("全部")} to={"/group/"+this.state.group.id} activeOnlyWhenExact={true}/>
                </div>
                {/* <div className="free">
                    <ActivityLink label=label={t("免费")} to="/free" />
                </div> */}
                <div className="paid">
                    <ActivityLink label={t("付费")} to={"/group/paid/"+this.state.group.id} />
                </div>
                <div className="media">
                <ActivityLink label={t("图片")} to={"/group/media/"+this.state.group.id} />
                </div>
            </div>

            { this.state.feeds.length > 0 && <div><ul className="feedlist">
                {this.state.feeds.map( (item) => <FeedItem data={item} key={item.id} in_group={true}/> ) } 
            </ul>
            { this.state.loading && <div className="hcenter"><Spinner intent={Intent.PRIMARY} small={true} /></div> }
            <VisibilitySensor onChange={(e)=>this.feedloading(e)}/>
            </div>
            }

            { this.state.feeds.length < 1 && <NonIdealState className="padding40"
                    visual="search"
                    title={t("还没有内容")}
                    description={t("没有符合条件的内容")}
                    
                /> }</Fragment>
            
            }

            
           
            

            
            
        </div>;

        const left = <div className="groupleft px10list">
            <GroupCard group={this.state.group}/>
            <div className="groupactionbar">
            { !is_member && this.state.loaded && <div className="freenotice px10list">
                <div className="button">
                <Button text={t("免费订阅")} intent={Intent.PRIMARY} onClick={()=>this.join(this.state.group.id)} large={true}/>
                </div>
                <div className="detail">
                {t("订阅后可在首页显示更新")}
                </div>
            </div>
            }
            { is_member && !is_admin && this.state.loaded && <div className="membernotice px10list">
                <div className="button">
                <BuyVipButton group={this.state.group} className="pt-large pt-intent-primary" text={t("购买VIP")+" · "+web3.utils.fromWei( this.state.group.price_wei + '' , 'ether' )+'Ξ'} renewaltext={t("VIP订户 · 续费")}/>
                </div>
                <div className="detail">
                { this.state.paid_feed_count > 0 ? t("VIP订户可以看到栏目中的"+ this.state.paid_feed_count +"篇付费内容") : t("VIP订户可以看到栏目中的付费内容") }
                </div> 
                <div className="quit">
                    <span onClick={()=>this.quit(this.state.group.id)}>{t("取消订阅")}</span>
                </div>    
            </div>
            }

            </div>
            { inGroup( this.state.group.id , this.props.store.user.admin_groups ) && this.state.loaded && <div className="px10list whitebox vcenter hcenter flexcol">
                <div><ButtonGroup large={false} minimal={true}>
                {this.state.group.todo_count>0 && <Button text={t("投稿 · ")+this.state.group.todo_count} icon="inbox" intent={Intent.PRIMARY} onClick={()=>this.contribute()} />} 
                {this.state.group.todo_count < 1 && <Button text={t("投稿")} icon="inbox" intent={Intent.NONE} onClick={()=>this.contribute()} />} 

                {this.state.group.member_count>0 && <Button text={this.state.group.member_count} icon="people" intent={Intent.NONE} onClick={()=>this.member()}/>}

                {this.state.group.member_count<1 && <Button  icon="people" intent={Intent.NONE} onClick={()=>this.member()}/>}

                { is_admin && <Button icon="cog" intent={Intent.NONE} onClick={()=>this.settings()}/>}

                </ButtonGroup></div>
                
                {/* <div className="detail">
                {t("管理选项")}
                </div> */}
                
            </div>
            }
        </div>;

        return <DocumentTitle title={this.state.group.name+'@'+t(this.props.store.appname)}><Cloumn3Layout left={left} main={main} /></DocumentTitle>
    }
}