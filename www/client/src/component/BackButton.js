import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import { Button } from "@blueprintjs/core";

@withRouter
@translate()
@inject("store")
@observer
export default class BackButton extends Component
{
    render()
    {
        return this.props.history.length > 1 && <div className="backbutton">
        <Button text={this.props.t("点此返回")} icon="arrow-left" fill={true} large={true} minimal={true} onClick={()=>this.props.history.goBack(1)} />
        </div>;
    }
}