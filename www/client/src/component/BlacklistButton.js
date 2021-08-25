import React, { Component,Fragment } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import { isApiOk, toInt, showApiError, toast } from '../util/Function';
import { Button } from "@blueprintjs/core";

@withRouter
@translate()
@inject("store")
@observer
export default class BlacklistButton extends Component
{
    state = {"in":false}
    
    componentDidMount()
    {
        this.checkUser();
    }

    async checkUser()
    {
        if( this.props.uid > 0 )
        {
            const { data } = await this.props.store.checkUserInBlacklist( this.props.uid );
            if( isApiOk( data ) )
            {
                this.setState({"in":toInt( data.data ) ===1})
            }else
                showApiError( data , this.props.t );
        }
        
    }

    async setBlacklist( uid , status )
    {
        const { t } = this.props;
        const { data } = await this.props.store.setUserInBlacklist( uid , status );
        if( isApiOk( data ) )
        {
            if( toInt( data.data )  === 1 )
                toast(t("已将此用户加入黑名单，你可以在设置中移出"));
            else
                toast(t("已将此用户移出黑名单"));

            this.checkUser();    
        }
        else
            showApiError( data , t  );
    }
    
    render()
    {
        const { t } = this.props;
        return <Fragment>
            {this.state.in && <Button className={this.props.className} text={t("移出黑名单")} icon="small-minus" onClick={()=>this.setBlacklist( this.props.uid , 0 )}/>}
            {!this.state.in && <Button className={this.props.className} text={t("加入黑名单")} icon="small-plus" onClick={()=>this.setBlacklist( this.props.uid , 1 )}/>}
        </Fragment>;
    }
}