import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter } from 'react-router-dom';

@inject("store")
@observer
export default class Text extends Component
{
    render()
    {
        return <div className="ttop">{this.props.store.count}
        <button onClick={()=>{this.props.store.plus()}}>+</button>  
        </div>;
    }
}