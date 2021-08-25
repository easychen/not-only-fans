import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter, Link } from 'react-router-dom';
import { translate } from 'react-i18next';

import { Colors, Icon, RadioGroup, Radio } from "@blueprintjs/core";
// import LMIcon from '../Icon';
import UserLink from '../component/UserLink'; 
import UserAvatar from '../component/UserAvatar';

import MyTime from '../component/MyTime'; 


import FeedText from '../component/FeedText';
import { isApiOk, showApiError } from '../util/Function';

@withRouter
@translate()
@inject("store")
@observer
export default class ContributeItem extends Component
{
    state = {"item":null}
    componentDidMount()
    {
        this.setState({"item":this.props.data})
    }
    
    async change( gid , fid , e )
    {
        const { t } = this.props;
        const status = parseInt( e.target.value, 10 );

        const { data } = await this.props.store.updateContribute( gid, fid , status );
        if( isApiOk( data ) )
        {
            console.log( data );
            // window.location.reload();
            let new_to_groups = [];
            this.state.item.to_groups.map( (group) => {
                if( group.id == gid ) group.status = status;
                new_to_groups.push( group );
            } );

            let new_item = this.state.item;
            new_item.to_groups = new_to_groups;
            console.log( new_item );
            this.setState({"item":new_item});
            
        }else
            showApiError( data , t );
        
    }
    
    render()
    {
        const { t } = this.props;
        const item = this.state.item;
        const fid = this.props.fid ? 'FID='+this.props.fid : '';
        
        if( !item ) return  null;

        if(item.images && !Array.isArray(item.images) && item.images.length > 0)
            item.images = JSON.parse( item.images );

        if(item.files && !Array.isArray(item.files) && item.files.length > 0)
            item.files = [JSON.parse( item.files )];  

        console.log( item );
        
        let i = 0;

        return <li>
            <UserAvatar data={item.user} className="avatarbox"/>
            <div className="feedbox">
                { (item.forward_is_paid > 0 || item.is_paid > 0 )  && <div className="paid"><Icon icon="dollar" color={Colors.LIGHT_GRAY3} title={t("此内容VIP订户可见")}/></div> }
                <div className="userbox">
                    <div className="name"><UserLink data={item.user} /><span>@{item.user.username}</span></div>
                    <div className="time">
                    <MyTime date={item.timeline} />
                    </div>
                </div>
                <div className="feedcontent">
                <FeedText text={item.text} more={t("显示更多")} less={<div className="top10">{t("↑收起")}</div>}/>
                <span className="explain">{fid}</span>

                { item.images && item.images.length > 0 && 
                    <ul className="photos">
                        { item.images.map( ( image ) => <li key={i++}>
                            <a href={image.orignal_url}target="_blank"><img src={image.thumb_url} alt="cover"/></a>
                        </li> 
                        )
                        }
                    </ul>
                }

                { item.files && item.files.length > 0 && <ul className="files">
                    { item.files.map( ( file ) => <li key={i++}>
                        <a href={file.url} target="_blank"><Icon icon="box" iconSize={18}/>{file.name}</a>
                    </li> 
                    )
                    
                    }
                    
                    
                </ul>
                }

                </div>
                {item.to_groups && <div className="grouplistbar">
                {t("投稿给")} <ul>
                    { item.to_groups.map( ( group ) => <li key={i++}>
                     <Link to={'/group/'+group.id} target="_blank" className="grouptitle">{group.name}</Link> 
                     <RadioGroup inline={true} className="radiobox" selectedValue={parseInt(group.status,10)} onChange={(e)=>this.change(group.id , item.id , e)}>
                        <Radio value={0}>{t("待处理")}</Radio>
                        <Radio value={1}>{t("通过")}</Radio>
                        <Radio value={2}>{t("拒稿")}</Radio>
                        {/* <Radio value={3}>{t("稍后处理")}</Radio> */}
                     </RadioGroup>     
                     </li> 
                    )
                    }
                    </ul>
                </div>}

                
                
            </div>
        </li>  ;
    }
}