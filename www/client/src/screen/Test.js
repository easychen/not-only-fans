import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { Link } from "react-router-dom";
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';

import Cloumn3Layout from '../component/Cloumn3Layout';
import UserCard from '../component/UserCard';
import DocumentTitle from 'react-document-title';

@withRouter
@translate()
@inject("store")
@observer
export default class Test extends Component
{
    handleData(data) 
    {
        console.log( data );
    }
    
    render()
    {
        const { t } = this.props;
        const main = <div></div>;
        return <DocumentTitle title={''+'@'+t(this.props.store.appname)}><Cloumn3Layout left={<UserCard/>} main={main} /></DocumentTitle>;
    }
}