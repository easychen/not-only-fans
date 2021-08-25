import React, { Component } from 'react';
import { observer , inject } from 'mobx-react';
import { withRouter } from 'react-router-dom';
import { translate } from 'react-i18next';
import { Overlay } from "@blueprintjs/core";
import PublishBox from '../component/PublishBox';

@withRouter
@translate()
@inject("store")
@observer
export default class FloatEditor extends Component
{
    render()
    {
        const store = this.props.store;
        return <Overlay isOpen={store.float_editor_open} onClose={()=>{store.float_editor_open = !store.float_editor_open}}>
        <div className="editorinoverlay">
            <PublishBox  groups={store.user.groups} className="editor" onClose={()=>{store.float_editor_open=false;}} onFinish={()=>{store.float_editor_open=false;}} />
        </div>
        
        </Overlay>;
    }
}