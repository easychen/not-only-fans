import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import { Icon } from "@blueprintjs/core";
import Web3 from 'web3';
// import BuyVipButton from '../component/BuyVipButton';

@translate()
@inject("store")
@withRouter
@observer
export default class GroupCard extends Component
{
    render()
    {
        const web3 = new Web3(Web3.givenProvider);
        const { t , group } = this.props;
        const price = group.price_wei && group.price_wei  > 0 ? web3.utils.fromWei( group.price_wei + '' , 'ether' ) : 0;
        
        return <div className="groupcard">
                <div className="groupheader">
                    <img src={group.cover} alt="" className="cover"/>
                    <h1>{group.name}</h1>
                </div>
                <div className="groupnav">
                    
                    <div className="infos">{t("订户")}<span>{group.member_count} </span></div>
                    <div className="infos">{t("内容")}<span>{group.feed_count}</span></div>
                    <div className="infos">{t("VIP")}<span title="{price}ETH">{price}<small>Ξ</small></span></div>                           
            
                </div>
        </div>;
    }
}