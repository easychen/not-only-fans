import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { Tooltip , Menu , MenuItem, Popover, PopoverInteractionKind, Position, Icon, TextArea, Button, Intent, Colors, Switch } from "@blueprintjs/core";

import ActivityLink from '../util/ActivityLink';
import { handeleBooleanGlobal } from '../util/Function';
import TimeAgo from 'react-timeago';
import cnStrings from 'react-timeago/lib/language-strings/zh-CN';
import buildFormatter from 'react-timeago/lib/formatters/buildFormatter';

import LMIcon from '../Icon';
import LangIcon from '../component/LangIcon';
import Header from '../component/Header';
import UserCard from '../component/UserCard';
import { t } from 'i18next/dist/commonjs';
import { translate, Trans } from 'react-i18next';

@withRouter
@translate()
@inject("store")
@observer
export default class Feed extends Component
{
    componentWillMount()
    {
        if( !this.props.store.user.token || this.props.store.user.token.length < 32 ) this.props.history.replace('/login');
        console.log( this.props.store.user );
        
    }
    
    render()
    {
        const {appname , hot_groups , draft_viponly, new_feed_count, new_notice_count, my_feeds  } = this.props.store;

        const formatter = buildFormatter(cnStrings);

        let i = 0;

        return <div> 
            <Header />
            <div className="middle">
                <div className="contentbox">
                    <div className="leftside">
                        <UserCard />
                        <div className="grouplist">
                            
                        </div>
                    </div>           
                    
                    <div className="main">
                        <div className="publishbox">
                        <TextArea className="box" placeholder="今天有什么好东西分享到栏目？"
                        />

                        <div className="action">
                            <div className="icons" >
                                <Icon icon="media" iconSize={20} color={Colors.GRAY4}/>
                                {/* 
                                <Icon icon="document" iconSize={20} color={Colors.GRAY4}/> */}
                                { draft_viponly && <Icon icon="paperclip" iconSize={20} color={Colors.GRAY4}/>}

                            </div> 
                            

                            { draft_viponly && <div className="type">
                                <Switch checked={draft_viponly} label={t("内容VIP可见")} large={true} onChange={(e)=>handeleBooleanGlobal(e , 'draft_viponly')} />
                            </div> }

                            { !draft_viponly && <div className="type">
                                <Switch className="gray5" checked={draft_viponly} label={t("内容订户可见")} large={true} onChange={(e)=>handeleBooleanGlobal(e , 'draft_viponly')} />
                            </div> }
                            
                            <div className="button">
                                <div className="togroup">
                                <Popover content={t("栏目多选")} position={Position.BOTTOM} interactionKind={PopoverInteractionKind.CLICK}>
                                <a>{t("选择栏目")}<Icon icon="caret-down"/></a>
                                </Popover> 
                                </div>
                                <Button text={t("发送")} intent={Intent.PRIMARY} large={true}/>
                            </div>
                        </div>
                        {/* action end */}

                        </div>
                        {/* publishbox end */}
                        
                        

                        <div className="feedfilter">
                            <div className="hot">
                            <ActivityLink label={t("热门")} to="/hot" activeOnlyWhenExact={true}/>
                            </div>
                            <div className="all">
                            <ActivityLink label={t("全部")} to="/" activeOnlyWhenExact={true}/>
                            </div>
                            {/* <div className="free">
                                <ActivityLink label={t("免费")} to="/free" />
                            </div> */}
                            <div className="paid">
                                <ActivityLink label={t("VIP")} to="/paid" />
                            </div>
                            <div className="media">
                            <ActivityLink label={t("图片")} to="/media" />
                            </div>
                        </div>

                        {/* 新动态计数  */}
                        { parseInt( new_feed_count , 10 ) > 0 &&
                        <Trans i18nKey="HasNewFeed">
                        <div className="newfeed">有{{new_feed_count}}条新的动态，点此刷新</div>
                        </Trans>
                        }
                        {/* 新动态计数 end   */}

                        { my_feeds.length > 0 && <ul className="feedlist">
                        {my_feeds.map( (item) => 
                        
                        <li key={item.id}>
                            <div className="avatarbox">
                                <img src={item.user.avatar} />
                            </div>
                            <div className="feedbox">
                                { item.vip_only && <div className="paid"><Icon icon="dollar" color={Colors.LIGHT_GRAY3} title={t("此内容VIP订户可见")}/></div> }
                                <div className="userbox">
                                  <div className="name">{item.user.nickname}<span>@{item.user.username}</span></div>
                                  <div className="time">
                                  <TimeAgo date={item.created_at} formatter={formatter} />
                                  &nbsp;·&nbsp;
                                  来自&nbsp;<Link to={'/group/'+item.from_id} target="_blank" >{item.from} </Link> 
                                  </div>
                                </div>
                                <div className="feedcontent">
                                {item.text}
                                { item.photos && item.photos.length > 0 && <ul className="photos">
                                    { item.photos.map( ( photo ) => <li key={i++}>
                                        <a href={photo.origin}target="_blank"><img src={photo.cover} alt="cover"/></a>
                                    </li> 
                                    )
                                    
                                    }
                                    
                                    
                                </ul>
                                }
                                { item.files && item.files.length > 0 && <ul className="files">
                                    { item.files.map( ( file ) => <li key={i++}>
                                        <a href={file.url}target="_blank"><Icon icon="box" iconSize={18}/>{file.name}</a>
                                    </li> 
                                    )
                                    
                                    }
                                    
                                    
                                </ul>
                                }
                                </div>
                                <div className="actionbar">
                                    <div className="share">
                                        <LMIcon name="share" size={20} color={Colors.GRAY5} />{ item.share_count > 0 && <span>{item.share_count}</span> }
                                    </div>
                                    <div className="comment">
                                        <LMIcon name="comment" size={20} color={Colors.GRAY5} />{ item.comment_count > 0 && <span>{item.comment_count}</span> }
                                    </div>
                                    <div className="up">
                                        <LMIcon name="up" size={20} color={Colors.GRAY5} />{ item.up_count > 0 && <span>{item.up_count}</span> }
                                    </div>
                                
                                </div>
                            </div>
                        
                        
                        </li> 
                        
                        )}
                        </ul> }

                    </div>
                    
                    <div className="rightside">
                        <div className="promogroup">
                        <h1><Icon icon="flame" iconSize={20}/>{t("热门栏目")}</h1>
                        <ul>
                        {hot_groups && hot_groups.length > 0 &&hot_groups.map( (item) => <li key={item.id}><img src={item.cover} /><ActivityLink to={"/group/"+item.id} label={item.title}/></li> ) }
                                
                        </ul> 
                        </div>        
                        <div className="copyright">
                        © 2020 Fi-Mi.com  
                        </div>
                    </div>
                </div>
            </div>
        </div>;
    }
}