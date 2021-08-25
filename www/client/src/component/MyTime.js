import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';

import TimeAgo from 'react-timeago';
import cnStrings from 'react-timeago/lib/language-strings/zh-CN';
import twStrings from 'react-timeago/lib/language-strings/zh-TW';
import enStrings from 'react-timeago/lib/language-strings/en';
import jaStrings from 'react-timeago/lib/language-strings/ja';
import buildFormatter from 'react-timeago/lib/formatters/buildFormatter';

@withRouter
@translate()
@inject("store")
@observer
export default class MyTime extends Component
{
    render()
    {
        let formatter = buildFormatter(enStrings);
        switch( this.props.i18n.language.toLowerCase())
        {
            case 'zh-cn' : 
                formatter = buildFormatter(cnStrings);
                break;
            
            case 'zh-tw' : 
                formatter = buildFormatter(twStrings);
                break;
            
            case 'jp' : 
                formatter = buildFormatter(jaStrings);
                break;
            
            case 'en' : 
                formatter = buildFormatter(enStrings);    
        }
        
        

        return <TimeAgo date={this.props.date} formatter={formatter} />;
    }
}