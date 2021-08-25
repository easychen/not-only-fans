import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';

@translate()
@inject("store")
@withRouter
@observer
export default class GroupNotice extends Component
{
    render()
    {
        const { user } = this.props.store;
        const { t } = this.props;
        
        return <div className="groupnotice">
            {t("没有可用的栏目，")}
            <Link to="/group/create">{t("创建栏目")}</Link> <span> | </span> <Link to="/group/list">{t("订阅栏目")}</Link>
        </div>;
    }
}