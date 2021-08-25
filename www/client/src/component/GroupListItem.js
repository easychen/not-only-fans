import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter, Link } from 'react-router-dom';
import { translate } from 'react-i18next';
import Web3 from 'web3';
import { Button, Icon } from "@blueprintjs/core";

@withRouter
@translate()
@inject("store")
@observer
export default class GroupListItem extends Component
{
    constructor( props )
    {
        super( props );
        const data = this.props.data ? this.props.data : null;
        this.state = {"group":data};
    }

    render()
    {
        const web3 = new Web3(Web3.givenProvider);
        const { t } = this.props;
        const item = this.state.group;
        if( !item ) return null;
        
        return <li key={item.id}>
        <div className="cover"><img src={item.cover}  /></div>
        <div className="info">
            <div className="title"><Link to={"/group/"+item.id}>{item.name}</Link></div>
            <div className="count">{web3.utils.fromWei( item.price_wei+'','ether' ) }Ξ&nbsp;·&nbsp; {item.member_count} {t("订户")}  {item.feed_count > 0 && <span>&nbsp;·&nbsp; {item.feed_count} {t("内容")}</span>} </div>
        </div> 
        <div className="action">
            <Button icon="arrow-right" minimal={true} onClick={()=>this.props.history.push("/group/"+item.id)}/>
        </div>   
        </li>;
    }
}