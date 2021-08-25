import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter } from 'react-router-dom';
import QRCode from 'qrcode.react';

@withRouter
@inject("store")
@observer
export default class QRImage extends Component
{
    state = {"value":this.props.value,"img_url":""};
    
    componentDidMount()
    {
        this.ck=setInterval( ()=>this.check() , 500 );
    }

    check()
    {
        const canvas = document.getElementById('theqr__inbox');
        if( canvas )
        {
            this.setState({"img_url":canvas.toDataURL("image/png")});
            clearInterval( this.ck );
        }
    }
    
    
    render()
    {
        // <QRCode value={this.state.qr_code}  /> 
        return <div className={this.props.className}>
            { this.state.img_url.length > 0 ? <img src={this.state.img_url} /> : <QRCode size={512} value={this.state.value} includeMargin={true} id="theqr__inbox" includeMargin={true} /> }
        </div>
         ;
    }
}