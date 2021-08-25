import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';

@withRouter
@translate()
@inject("store")
@observer
export default class ClassNamePlaceHolder extends Component
{
    render()
    {
        return <div>ClassNamePlaceHolder</div>;
    }
}